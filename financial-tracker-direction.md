# Financial Tracker Engineering Direction

## 1. Purpose

Build a personal financial tracker with a simple, maintainable architecture.

Primary goals:

- Correct financial calculations
- Strong transaction integrity
- Clear ownership and authorization
- Auditable financial history
- Easy-to-understand domain code
- Avoid premature complexity and overengineering

The application should start as a **modular monolith**.

Do NOT introduce microservices, Kafka, CQRS, event sourcing, GraphQL, Kubernetes, or other distributed infrastructure unless a concrete requirement appears.

---

# 2. Recommended Stack

## Mobile

- Flutter
- Riverpod for state management
- GoRouter for navigation
- Dio for HTTP
- Drift/SQLite only when local persistence or offline support is actually required

## Backend

- Laravel 13
- PHP 8.3+
- REST API
- Laravel Sanctum for authentication
- Laravel Policies for authorization
- Laravel Form Requests for request validation
- Laravel API Resources where useful

## Database

- PostgreSQL

## Web/Admin

- Laravel + Livewire + Tailwind CSS

## Optional infrastructure

- Redis only when caching/queue requirements justify it
- S3-compatible storage only when attachments are required

---

# 3. Core Architecture

Use a modular monolith.

Suggested Laravel structure:

```text
app/
├── Domain/
│   ├── Account/
│   │   ├── Actions/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── Services/
│   │
│   ├── Transaction/
│   │   ├── Actions/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── Services/
│   │
│   ├── Transfer/
│   │   ├── Actions/
│   │   ├── Models/
│   │   └── Services/
│   │
│   ├── Budget/
│   ├── Category/
│   └── Audit/
│
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
│
└── Support/
```

Keep controllers thin.

Business rules belong in domain actions/services, not controllers.

Do not create interfaces, repositories, factories, managers, strategies, or abstractions unless they solve an actual problem.

---

# 4. Database Design

## Users

Use Laravel's standard users table.

## Accounts

An account represents a source or container of money.

```text
accounts
--------
id
user_id
name
type
currency
initial_balance
is_active
created_at
updated_at
```

Supported account types can initially be:

```text
cash
bank
e_wallet
credit_card
investment
other
```

Every account must belong to exactly one user.

Use foreign keys.

---

# 5. Transactions

Core table:

```text
transactions
------------
id
account_id
category_id
type
amount
currency
transaction_date
description
reference
status
created_at
updated_at
deleted_at
```

Transaction types:

```text
income
expense
adjustment
```

Transfers are handled separately.

## Amount convention

Always store `amount` as a positive value.

Example:

```text
income  = 5,000,000
expense =   500,000
```

Do NOT store expense as a negative number.

The transaction type determines the financial direction.

---

# 6. Money Representation

Never use FLOAT or DOUBLE for monetary values.

Preferred initial PostgreSQL type:

```sql
DECIMAL(20, 2)
```

For example:

```sql
amount DECIMAL(20, 2) NOT NULL
```

Use explicit rounding rules.

Do not silently round values in random parts of the application.

All monetary calculations should happen in a dedicated domain/service layer.

---

# 7. Categories

Suggested table:

```text
categories
----------
id
user_id
name
type
is_system
created_at
updated_at
```

Category types may include:

```text
income
expense
```

Recommended constraint:

```text
UNIQUE(user_id, name)
```

A user should not be able to create duplicate category names for the same scope unless there is a deliberate product requirement.

---

# 8. Transfers

A transfer between accounts is NOT an expense.

Example:

```text
BCA -> Gopay
Rp 5,000,000
```

This must not appear as:

```text
expense = Rp 5,000,000
```

because the user's total wealth has not decreased.

Use:

```text
transfers
---------
id
from_account_id
to_account_id
amount
currency
transaction_date
description
created_at
updated_at
```

A transfer should produce two ledger effects:

```text
BCA
-5,000,000

Gopay
+5,000,000
```

Total effect on wealth:

```text
0
```

---

# 9. Ledger / Balance Strategy

For a reliable financial system, prefer an immutable-ish ledger model.

Suggested:

```text
ledger_entries
--------------
id
account_id
transaction_id
direction
amount
created_at
```

Direction:

```text
debit
credit
```

Balance can then be derived from the ledger.

Conceptually:

```text
balance =
opening balance
+ credits
- debits
+ adjustments
```

Do not make `accounts.current_balance` the only source of truth.

If a cached/current balance is introduced later for performance, it must be treated as a derived value and kept consistent with the ledger.

---

# 10. Financial Invariants

These invariants are mandatory.

## Account balance

```text
balance =
opening balance
+ credits
- debits
+ adjustments
```

## Transfer

For every transfer:

```text
total debit = total credit
```

## Ownership

Every financial resource must be owned by or traceable to exactly one user.

For example:

```text
Transaction
    -> Account
        -> User
```

A user must never be able to access another user's financial records by changing an ID in the URL or API request.

---

# 11. Database Transactions

Financial mutations that affect multiple records MUST use database transactions.

Example:

```php
DB::transaction(function () {
    // create transfer
    // create debit ledger entry
    // create credit ledger entry
    // create audit record
});
```

If any operation fails:

```text
ROLLBACK
```

Nothing should be partially committed.

Never perform a multi-step financial mutation without atomicity.

---

# 12. Authorization

Never trust IDs supplied by the client.

Bad:

```php
Account::findOrFail($id);
```

Preferred:

```php
Account::where('user_id', auth()->id())
    ->findOrFail($id);
```

Also use Laravel Policies.

Examples:

```text
AccountPolicy
TransactionPolicy
TransferPolicy
BudgetPolicy
```

Authorization must be enforced on the backend.

Frontend visibility is NOT security.

Hiding a button does not prevent unauthorized API requests.

---

# 13. Audit Trail

Create an audit log.

Suggested table:

```text
audit_logs
----------
id
user_id
action
auditable_type
auditable_id
old_values
new_values
ip_address
user_agent
created_at
```

Example:

```text
action:
transaction.updated
```

Old values:

```json
{
  "amount": 500000,
  "category_id": 4
}
```

New values:

```json
{
  "amount": 750000,
  "category_id": 7
}
```

Audit logs should be append-only.

Do not allow normal application flows to modify historical audit entries.

---

# 14. Deleting Financial Transactions

Do not hard-delete financial history by default.

Preferred approaches:

1. Soft delete for user-facing removal
2. Reversal/void for financial corrections

If a transaction was wrong:

```text
original transaction
        ↓
reversal
        ↓
correct transaction
```

Do not pretend that the original transaction never existed.

Historical financial facts should remain reconstructable.

---

# 15. Currency

Every monetary record should have an explicit currency.

Use ISO 4217 codes:

```text
IDR
USD
SGD
MYR
JPY
```

Initial implementation may support only IDR.

However, keep the schema capable of storing currency.

Do NOT implement full multi-currency conversion until it is actually required.

If multi-currency is implemented later, define explicit fields and rules for:

```text
amount
currency
exchange_rate
base_amount
base_currency
```

Never perform implicit currency conversion.

---

# 16. Validation

Use two layers.

## Application validation

Laravel Form Requests:

```php
'amount' => [
    'required',
    'numeric',
    'min:0.01',
],
```

## Database validation

Use:

- Foreign keys
- NOT NULL where appropriate
- UNIQUE constraints
- CHECK constraints where supported and useful

Application validation improves UX.

Database constraints protect data integrity.

Both are required.

---

# 17. Soft Deletes and Historical Data

Use soft deletes where appropriate.

For financial records, prefer preserving historical information.

Do not use cascading deletes that accidentally destroy financial history.

Be especially careful with:

```text
users
accounts
transactions
ledger_entries
audit_logs
```

Deletion behavior must be explicitly designed.

---

# 18. Reporting

Reports should be derived from transaction/ledger data.

Examples:

```text
Monthly income
Monthly expense
Net cash flow
Category spending
Account balance
Budget utilization
```

Avoid storing redundant report totals unless there is a measured performance requirement.

Example:

```text
monthly_expense
```

should initially be calculated from source data, not maintained as another mutable value.

---

# 19. Recommended Development Order

Implement in this order:

```text
1. Authentication
2. Users
3. Accounts
4. Categories
5. Transactions
6. Ledger entries
7. Transfers
8. Balance calculation
9. Authorization policies
10. Audit trail
11. Budgets
12. Recurring transactions
13. Reports/dashboard
14. Attachments
15. Notifications
```

Do not start with dashboard/UI polish before the financial domain is correct.

---

# 20. Testing Strategy

Financial logic requires automated tests.

Minimum required tests:

## Transaction

- Create income
- Create expense
- Update transaction
- Reverse transaction
- Reject invalid amount
- Reject unauthorized access

## Transfer

- Transfer between accounts
- Correct debit
- Correct credit
- Balance remains consistent
- Rollback when one operation fails
- Prevent transfer between unauthorized accounts

## Authorization

Test:

```text
User A cannot access User B's account
User A cannot access User B's transactions
User A cannot modify User B's categories
User A cannot create a transaction against User B's account
```

## Financial invariants

Test that:

```text
sum(debits) == sum(credits)
```

for transfers and any ledger operation that should balance.

---

# 21. API Guidelines

Use REST.

Example endpoints:

```text
GET    /api/accounts
POST   /api/accounts
GET    /api/accounts/{account}
PUT    /api/accounts/{account}
DELETE /api/accounts/{account}

GET    /api/transactions
POST   /api/transactions
GET    /api/transactions/{transaction}
PUT    /api/transactions/{transaction}
DELETE /api/transactions/{transaction}

POST   /api/transfers
POST   /api/transactions/{transaction}/reverse

GET    /api/reports/monthly
GET    /api/reports/categories
```

Do not introduce GraphQL unless a concrete requirement justifies it.

---

# 22. API Security

Use:

- HTTPS
- Sanctum
- Authorization Policies
- Request validation
- Rate limiting where appropriate
- Server-side ownership checks
- Consistent API error responses

Never trust:

```text
user_id
account_id
owner_id
```

from the client without verifying ownership.

The authenticated user identity comes from the authentication mechanism.

---

# 23. Idempotency

For operations that may be retried, especially financial mutations, consider idempotency.

For example:

```text
POST /api/transfers
Idempotency-Key: <unique-key>
```

If the same request is submitted twice because of network retry, it must not create two transfers.

This is especially important for mobile clients because mobile networks have a charming habit of pretending a request disappeared.

Implement this when the API begins supporting operations where duplicate submission has financial consequences.

---

# 24. Error Handling

Never expose internal exceptions or SQL details to clients.

Use consistent responses.

Example:

```json
{
    "message": "Transaction could not be created.",
    "errors": {
        "amount": [
            "The amount must be greater than zero."
        ]
    }
}
```

Unexpected exceptions should be logged server-side.

---

# 25. Performance

Start simple.

Use:

- PostgreSQL indexes
- Proper foreign keys
- Eager loading where necessary
- Pagination
- Query optimization

Recommended indexes include:

```text
accounts(user_id)

transactions(account_id)
transactions(category_id)
transactions(transaction_date)
transactions(account_id, transaction_date)

ledger_entries(account_id)
ledger_entries(transaction_id)

audit_logs(user_id)
audit_logs(auditable_type, auditable_id)
```

Do not add Redis, Elasticsearch, read replicas, or other infrastructure until profiling shows a real need.

---

# 26. Things NOT to Build Initially

Do not introduce:

```text
Microservices
Kafka
RabbitMQ
CQRS
Event sourcing
GraphQL
Kubernetes
Elasticsearch
Multiple databases
Complex repository layers
Generic CRUD frameworks
Distributed transactions
Service mesh
```

The default architecture should remain:

```text
Flutter
   ↓
Laravel API
   ↓
PostgreSQL
```

Simple is a feature.

---

# 27. Coding Principles

Follow these rules:

1. Business logic should be explicit.
2. Financial calculations must be deterministic.
3. Money must never use floating-point arithmetic.
4. Financial mutations must be atomic.
5. Financial history must be reconstructable.
6. Authorization must happen server-side.
7. Database constraints must protect critical invariants.
8. Avoid unnecessary abstractions.
9. Prefer readable code over clever code.
10. Do not optimize before measuring.

Use OOP where it improves domain clarity.

Good:

```text
CreateTransaction
ReverseTransaction
CreateTransfer
CalculateAccountBalance
```

Bad:

```text
UniversalFinancialManagerFactoryStrategy
```

The second one is technically impressive in the same way a 400-line screwdriver is technically impressive.

---

# 28. Definition of Done for Financial Features

A financial feature is not complete until:

- [ ] Database schema exists
- [ ] Foreign keys/constraints are defined
- [ ] Request validation exists
- [ ] Authorization policy exists
- [ ] Domain action/service exists where needed
- [ ] Database transaction is used for multi-record mutations
- [ ] Audit trail is recorded where appropriate
- [ ] Financial invariants are preserved
- [ ] Automated tests cover happy paths
- [ ] Automated tests cover authorization failures
- [ ] Automated tests cover failure/rollback cases
- [ ] API response is consistent
- [ ] No floating-point money calculations exist

---

# 29. Priority

When making engineering decisions, prioritize:

```text
1. Data correctness
2. Security
3. Transaction integrity
4. Auditability
5. Maintainability
6. Performance
7. Developer convenience
8. UI polish
```

For a financial application, a beautiful dashboard showing incorrect numbers is worse than an ugly dashboard showing correct numbers.

Build the boring foundation first.
