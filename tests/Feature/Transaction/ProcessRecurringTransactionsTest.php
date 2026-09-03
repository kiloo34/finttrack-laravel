<?php

namespace Tests\Feature\Transaction;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use App\Modules\Category\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use App\Modules\Transaction\Domain\ValueObjects\ExecutionType;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\RecurringTransactionModel;
use App\Modules\Transaction\Infrastructure\Persistence\Eloquent\Models\TransactionModel;
use App\Modules\User\Infrastructure\Persistence\Eloquent\Models\UserModel;
use App\Notifications\RecurringTransactionExecutedNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProcessRecurringTransactionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_processes_due_recurring_transactions_and_creates_ledger_entries()
    {
        Notification::fake();
        Carbon::setTestNow('2026-09-01 10:00:00');

        $user = UserModel::factory()->create();
        $account = AccountModel::factory()->create(['user_id' => $user->id]);
        $category = CategoryModel::factory()->create(['user_id' => $user->id]);

        $recurring = RecurringTransactionModel::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 50000,
            'currency' => 'IDR',
            'frequency' => 'monthly',
            'execution_type' => ExecutionType::AutoComplete->value,
            'next_run_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'is_active' => true,
        ]);

        $this->artisan('transactions:process-recurring')
            ->assertExitCode(0);

        // Assert transaction was created
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'expense',
            'amount' => 50000,
            'status' => 'completed',
        ]);

        // Assert ledger entry was created
        $transaction = TransactionModel::first();
        $this->assertDatabaseHas('ledger_entries', [
            'transaction_id' => $transaction->id,
            'account_id' => $account->id,
            'amount' => 50000,
            'direction' => 'debit',
        ]);

        // Assert recurring transaction date advanced
        $recurring->refresh();
        $this->assertEquals(Carbon::now()->addMonth()->format('Y-m-d'), $recurring->next_run_date->format('Y-m-d'));

        // Assert notification sent
        Notification::assertSentTo($user, RecurringTransactionExecutedNotification::class);
    }

    public function test_it_does_not_process_future_recurring_transactions()
    {
        Carbon::setTestNow('2026-09-01 10:00:00');

        $user = UserModel::factory()->create();
        $account = AccountModel::factory()->create(['user_id' => $user->id]);

        RecurringTransactionModel::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'type' => 'expense',
            'amount' => 50000,
            'currency' => 'IDR',
            'frequency' => 'monthly',
            'execution_type' => ExecutionType::AutoComplete->value,
            'next_run_date' => Carbon::now()->addDays(5)->format('Y-m-d H:i:s'), // Future date
            'is_active' => true,
        ]);

        $this->artisan('transactions:process-recurring')
            ->assertExitCode(0);

        $this->assertDatabaseCount('transactions', 0);
    }
}
