<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel as Account;
use App\Modules\Budget\Infrastructure\Persistence\Eloquent\Models\BudgetModel as Budget;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel as Category;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel as Transaction;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel as User;

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'ConstantsSeeder']);
    $this->user = User::factory()->create();
    $this->account = Account::factory()->create(['user_id' => $this->user->id]);
    $this->category = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'expense']);
});

it('can create a budget successfully', function () {
    $data = [
        'category_id' => $this->category->id,
        'amount' => 1000000,
        'month' => 8,
        'year' => 2026,
    ];

    $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/budgets', $data);

    $response->assertStatus(201)
        ->assertJsonPath('data.category_id', $this->category->id)
        ->assertJsonPath('data.amount', 1000000)
        ->assertJsonPath('data.month', 8)
        ->assertJsonPath('data.year', 2026)
        ->assertJsonPath('data.spent_amount', 0)
        ->assertJsonPath('data.remaining_amount', 1000000)
        ->assertJsonPath('data.percentage', 0);

    $this->assertDatabaseHas('budgets', [
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'amount' => 1000000,
        'month' => 8,
        'year' => 2026,
    ]);
});

it('prevents creating duplicate budget for the same user, category, and period', function () {
    Budget::create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'amount' => 500000,
        'month' => 8,
        'year' => 2026,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/budgets', [
        'category_id' => $this->category->id,
        'amount' => 700000,
        'month' => 8,
        'year' => 2026,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['category_id']);
});

it('returns budget list with accurate spentAmount, remainingAmount, and percentage', function () {
    $budget = Budget::create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'amount' => 500000,
        'month' => 8,
        'year' => 2026,
    ]);

    // Expense in same month & year (valid)
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'type' => 'expense',
        'amount' => 150000,
        'transaction_date' => '2026-08-05',
    ]);

    // Another expense in same month & year (valid)
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'type' => 'expense',
        'amount' => 50000,
        'transaction_date' => '2026-08-15',
    ]);

    // Expense in different month (should not count)
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'type' => 'expense',
        'amount' => 100000,
        'transaction_date' => '2026-07-10',
    ]);

    // Income in same month & year (should not count)
    Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'type' => 'income',
        'amount' => 300000,
        'transaction_date' => '2026-08-20',
    ]);

    // Soft-deleted expense in same month & year (should not count)
    $deletedExpense = Transaction::factory()->create([
        'account_id' => $this->account->id,
        'category_id' => $this->category->id,
        'type' => 'expense',
        'amount' => 80000,
        'transaction_date' => '2026-08-22',
    ]);
    $deletedExpense->delete();

    $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/budgets?month=8&year=2026');

    // Total spent: 150,000 + 50,000 = 200,000
    // Remaining: 500,000 - 200,000 = 300,000
    // Percentage: (200,000 / 500,000) * 100 = 40.0%
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $budget->id)
        ->assertJsonPath('data.0.category_id', $this->category->id)
        ->assertJsonPath('data.0.amount', 500000)
        ->assertJsonPath('data.0.spent_amount', 200000)
        ->assertJsonPath('data.0.remaining_amount', 300000)
        ->assertJsonPath('data.0.percentage', 40);
});

it('can update budget amount', function () {
    $budget = Budget::create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'amount' => 500000,
        'month' => 8,
        'year' => 2026,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')->putJson("/api/budgets/{$budget->id}", [
        'amount' => 750000,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.amount', 750000);

    $this->assertDatabaseHas('budgets', [
        'id' => $budget->id,
        'amount' => 750000,
    ]);
});

it('can delete budget', function () {
    $budget = Budget::create([
        'user_id' => $this->user->id,
        'category_id' => $this->category->id,
        'amount' => 500000,
        'month' => 8,
        'year' => 2026,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')->deleteJson("/api/budgets/{$budget->id}");

    $response->assertStatus(204);

    $this->assertSoftDeleted('budgets', [
        'id' => $budget->id,
    ]);
});

it('enforces user isolation so User A cannot access User B budget', function () {
    $otherUser = User::factory()->create();
    $otherCategory = Category::factory()->create(['user_id' => $otherUser->id, 'type' => 'expense']);
    $otherBudget = Budget::create([
        'user_id' => $otherUser->id,
        'category_id' => $otherCategory->id,
        'amount' => 900000,
        'month' => 8,
        'year' => 2026,
    ]);

    // GET show should be 404
    $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/budgets/{$otherBudget->id}")
        ->assertStatus(404);

    // PUT update should be 404
    $this->actingAs($this->user, 'sanctum')
        ->putJson("/api/budgets/{$otherBudget->id}", ['amount' => 100000])
        ->assertStatus(404);

    // DELETE should be 404
    $this->actingAs($this->user, 'sanctum')
        ->deleteJson("/api/budgets/{$otherBudget->id}")
        ->assertStatus(404);

    // GET index should not include User B's budget
    $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/budgets?month=8&year=2026')
        ->assertStatus(200)
        ->assertJsonMissing(['id' => $otherBudget->id]);
});

