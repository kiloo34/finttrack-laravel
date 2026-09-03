# Event-Driven Architecture Pattern

In addition to the standard Domain-Driven Design (DDD) principles used in this project, **all side-effects must be handled using Domain Events and Event Listeners**.

## The Pattern

1. **Domain Entities as Event Generators:**
   Domain Entities (e.g., `Transaction`, `Account`) MUST include the `HasDomainEvents` trait.
   When a state changes (e.g., entity is created, updated, or deleted), the entity MUST record a domain event carrying primitive payload values, rather than performing side effects directly.
   Example: `$this->recordEvent(new TransactionCreated($this->id, ...));`

2. **Primitive Event Payloads:**
   Domain Events MUST only carry primitive data types (integers, strings, booleans, floats) or simple value objects. Do NOT pass full Eloquent models or fat entities inside the event to ensure it can be safely serialized or queued.

3. **Repository as Event Dispatcher:**
   The Infrastructure Layer (Eloquent Repositories) is responsible for flushing and dispatching events *after* successful database commits.
   The repository MUST wrap the entire persistence operation in a `DB::transaction()`. For updates, `lockForUpdate()` MUST be used.
   After `save()` is successful, the repository MUST call `$entity->flushEvents()` and dispatch them via Laravel's `event()` helper.

4. **Listeners for Side-Effects:**
   Any cross-module side effects (e.g., writing to Ledger, sending Notifications, calculating Cache) MUST be handled by Event Listeners inside the Application Layer of the receiving module.
   - For critical data integrity (like Ledger), the Listener MUST be synchronous.
   - For non-critical tasks (like Emails/Notifications), the Listener SHOULD be asynchronous (queued).

5. **Controllers and Use Cases:**
   Controllers and Use Cases MUST NOT contain side-effect logic (like writing to the Ledger or Audit directly). They must only trigger the primary domain logic. (e.g. Remove `$this->auditService->log()` from Controllers if they can be handled via Domain Events).
