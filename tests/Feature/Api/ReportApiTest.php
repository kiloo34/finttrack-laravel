<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel as Account;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel as Category;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel as Transaction;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;
use Carbon\Carbon;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'ConstantsSeeder']);

    // User A
    $this->user = User::factory()->create();
    $this->account = Account::factory()->create(['user_id' => $this->user->id]);
    $this->catFood = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Makanan',
        'type' => 'expense',
    ]);
    $this->catTransport = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Transportasi',
        'type' => 'expense',
    ]);
    $this->catSalary = Category::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Gaji',
        'type' => 'income',
    ]);

    // User B (for isolation test)
    $this->otherUser = User::factory()->create();
    $this->otherAccount = Account::factory()->create(['user_id' => $this->otherUser->id]);
    $this->otherCategory = Category::factory()->create([
        'user_id' => $this->otherUser->id,
        'name' => 'Belanja Lain',
        'type' => 'expense',
    ]);
});

afterEach(function () {
    Carbon::setTestNow();
});

it('calculates category breakdown nominal and percentage correctly', function () {
    // 2 transactions in Food = 1,000,000
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catFood->id,
        'type' => 'expense',
        'amount' => 600000,
        'transaction_date' => '2026-08-05',
    ]);
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catFood->id,
        'type' => 'expense',
        'amount' => 400000,
        'transaction_date' => '2026-08-10',
    ]);

    // 1 transaction in Transport = 500,000
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catTransport->id,
        'type' => 'expense',
        'amount' => 500000,
        'transaction_date' => '2026-08-15',
    ]);

    // Income in same month (should not be in expense breakdown)
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catSalary->id,
        'type' => 'income',
        'amount' => 3000000,
        'transaction_date' => '2026-08-01',
    ]);

    // Expense in another month (should not count)
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catFood->id,
        'type' => 'expense',
        'amount' => 200000,
        'transaction_date' => '2026-07-20',
    ]);

    // Soft-deleted expense (should not count)
    $deleted = Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catFood->id,
        'type' => 'expense',
        'amount' => 150000,
        'transaction_date' => '2026-08-20',
    ]);
    $deleted->delete();

    // Query category breakdown for month 8, year 2026
    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/reports/category-breakdown?type=expense&month=8&year=2026');

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.period.month', 8)
        ->assertJsonPath('data.period.year', 2026)
        ->assertJsonPath('data.total_overall', 1500000)
        ->assertJsonCount(2, 'data.items')
        // Ranked by total_amount descending
        ->assertJsonPath('data.items.0.category_id', $this->catFood->id)
        ->assertJsonPath('data.items.0.category_name', 'Makanan')
        ->assertJsonPath('data.items.0.total_amount', 1000000)
        ->assertJsonPath('data.items.0.percentage', 66.7)
        ->assertJsonPath('data.items.0.transaction_count', 2)
        ->assertJsonPath('data.items.1.category_id', $this->catTransport->id)
        ->assertJsonPath('data.items.1.category_name', 'Transportasi')
        ->assertJsonPath('data.items.1.total_amount', 500000)
        ->assertJsonPath('data.items.1.percentage', 33.3)
        ->assertJsonPath('data.items.1.transaction_count', 1);

    // Query with start_date and end_date
    $dateRangeResponse = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/reports/category-breakdown?type=expense&start_date=2026-08-01&end_date=2026-08-12');

    $dateRangeResponse->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.period.start_date', '2026-08-01')
        ->assertJsonPath('data.period.end_date', '2026-08-12')
        ->assertJsonPath('data.total_overall', 1000000)
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.category_name', 'Makanan')
        ->assertJsonPath('data.items.0.percentage', 100);
});

it('returns cashflow trend for last N months with accurate income, expense, and net', function () {
    Carbon::setTestNow('2026-10-15');

    // 6 months window: May 2026 to Oct 2026
    // Transactions in Aug 2026 (Month 8)
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catSalary->id,
        'type' => 'income',
        'amount' => 5000000,
        'transaction_date' => '2026-08-01',
    ]);
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catFood->id,
        'type' => 'expense',
        'amount' => 2000000,
        'transaction_date' => '2026-08-10',
    ]);

    // Transactions in Oct 2026 (Month 10)
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catSalary->id,
        'type' => 'income',
        'amount' => 3000000,
        'transaction_date' => '2026-10-02',
    ]);
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catTransport->id,
        'type' => 'expense',
        'amount' => 4000000,
        'transaction_date' => '2026-10-05',
    ]);

    // Transaction in Apr 2026 (7 months ago, out of 6 months window)
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catSalary->id,
        'type' => 'income',
        'amount' => 10000000,
        'transaction_date' => '2026-04-15',
    ]);

    // Soft-deleted transaction in Aug 2026
    $deleted = Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catFood->id,
        'type' => 'expense',
        'amount' => 999999,
        'transaction_date' => '2026-08-12',
    ]);
    $deleted->delete();

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/reports/cashflow-trend?months=6');

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonCount(6, 'data')
        // Oldest month: May 2026
        ->assertJsonPath('data.0.month', 5)
        ->assertJsonPath('data.0.year', 2026)
        ->assertJsonPath('data.0.month_label', 'May 2026')
        ->assertJsonPath('data.0.income', 0)
        ->assertJsonPath('data.0.expense', 0)
        ->assertJsonPath('data.0.net', 0)
        // August 2026 (index 3)
        ->assertJsonPath('data.3.month', 8)
        ->assertJsonPath('data.3.year', 2026)
        ->assertJsonPath('data.3.month_label', 'Aug 2026')
        ->assertJsonPath('data.3.income', 5000000)
        ->assertJsonPath('data.3.expense', 2000000)
        ->assertJsonPath('data.3.net', 3000000)
        // October 2026 (index 5)
        ->assertJsonPath('data.5.month', 10)
        ->assertJsonPath('data.5.year', 2026)
        ->assertJsonPath('data.5.month_label', 'Oct 2026')
        ->assertJsonPath('data.5.income', 3000000)
        ->assertJsonPath('data.5.expense', 4000000)
        ->assertJsonPath('data.5.net', -1000000);

    // Custom months parameter test: 3 months
    $threeMonthsResponse = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/reports/cashflow-trend?months=3');

    $threeMonthsResponse->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.0.month', 8)
        ->assertJsonPath('data.2.month', 10);
});

it('strictly enforces user isolation between different users', function () {
    // User A has 1 expense of 500,000 in Aug 2026
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->catFood->id,
        'type' => 'expense',
        'amount' => 500000,
        'transaction_date' => '2026-08-10',
    ]);

    // User B has expense 10,000,000 and income 20,000,000 in Aug 2026
    Transaction::factory()->create([
        'account_id' => $this->otherAccount->id,
        'category_id' => $this->otherCategory->id,
        'type' => 'expense',
        'amount' => 10000000,
        'transaction_date' => '2026-08-10',
    ]);
    Transaction::factory()->create([
        'account_id' => $this->otherAccount->id,
        'category_id' => $this->otherCategory->id,
        'type' => 'income',
        'amount' => 20000000,
        'transaction_date' => '2026-08-10',
    ]);

    Carbon::setTestNow('2026-08-15');

    // User A category breakdown must only see User A's 500,000
    $breakdownResponse = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/reports/category-breakdown?type=expense&month=8&year=2026');

    $breakdownResponse->assertOk()
        ->assertJsonPath('data.total_overall', 500000)
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.category_name', 'Makanan')
        ->assertJsonPath('data.items.0.total_amount', 500000);

    // User A cashflow trend must only see User A's expense and zero income
    $trendResponse = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/reports/cashflow-trend?months=1');

    $trendResponse->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.income', 0)
        ->assertJsonPath('data.0.expense', 500000)
        ->assertJsonPath('data.0.net', -500000);
});
