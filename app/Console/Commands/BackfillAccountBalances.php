<?php

namespace App\Console\Commands;

use App\Modules\Account\Infrastructure\Persistence\Eloquent\Models\AccountModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:backfill-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and populate current_balance for all accounts from ledger entries';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting account balance backfill...');

        $accounts = AccountModel::all();
        if ($accounts->isEmpty()) {
            $this->info('No accounts found.');

            return;
        }

        $bar = $this->output->createProgressBar($accounts->count());

        foreach ($accounts as $account) {
            $credits = DB::table('ledger_entries')
                ->where('account_id', $account->id)
                ->where('direction', 'credit')
                ->sum('amount');

            $debits = DB::table('ledger_entries')
                ->where('account_id', $account->id)
                ->where('direction', 'debit')
                ->sum('amount');

            $creditsStr = (string) $credits;
            $debitsStr = (string) $debits;
            $initialStr = (string) $account->initial_balance;

            if (! is_numeric($initialStr)) {
                throw new \RuntimeException('Invalid initial balance encountered during backfill.');
            }

            $diff = bcsub($creditsStr, $debitsStr, 2);
            $newBalance = bcadd($initialStr, $diff, 2);

            DB::table('accounts')
                ->where('id', $account->id)
                ->update(['current_balance' => $newBalance]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done! All account balances have been backfilled.');
    }
}
