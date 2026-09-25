<?php

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Carbon\Carbon;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = UserModel::factory()->create();
    $this->account = AccountModel::factory()->create([
        'user_id' => $this->user->id,
        'initial_balance' => '1000.00',
    ]);

    $this->expenseCategory = CategoryModel::factory()->create([
        'type' => 'expense',
        'user_id' => $this->user->id,
    ]);
});

test('can sync account balance via api on a valid date', function () {
    Carbon::setTestNow(Carbon::create(2023, 1, 28)); // 28th is valid

    $response = actingAs($this->user)->postJson("/api/accounts/{$this->account->id}/sync-balance", [
        'actual_balance' => '800.00',
        'category_id' => $this->expenseCategory->id,
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Account balance synced successfully.']);

    Carbon::setTestNow(); // reset
});

test('cannot sync account balance via api on an invalid date', function () {
    Carbon::setTestNow(Carbon::create(2023, 1, 15)); // 15th is invalid

    $response = actingAs($this->user)->postJson("/api/accounts/{$this->account->id}/sync-balance", [
        'actual_balance' => '800.00',
        'category_id' => $this->expenseCategory->id,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['sync']);

    Carbon::setTestNow(); // reset
});

test('cannot sync another users account', function () {
    Carbon::setTestNow(Carbon::create(2023, 1, 28));

    $otherUser = UserModel::factory()->create();

    $response = actingAs($otherUser)->postJson("/api/accounts/{$this->account->id}/sync-balance", [
        'actual_balance' => '800.00',
        'category_id' => $this->expenseCategory->id,
    ]);

    $response->assertStatus(404);

    Carbon::setTestNow(); // reset
});
