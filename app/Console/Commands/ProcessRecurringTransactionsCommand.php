<?php

namespace App\Console\Commands;

use App\Modules\Transaction\Application\UseCases\ProcessRecurringTransactionsUseCase;
use Illuminate\Console\Command;

class ProcessRecurringTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:process-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due recurring transactions and generate actual transactions.';

    /**
     * Execute the console command.
     */
    public function handle(ProcessRecurringTransactionsUseCase $useCase): int
    {
        $this->info('Starting to process recurring transactions...');

        $processedCount = $useCase->execute();

        $this->info("Successfully processed {$processedCount} recurring transactions.");

        return Command::SUCCESS;
    }
}
