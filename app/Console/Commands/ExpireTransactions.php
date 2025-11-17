<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NewConsultationController;

class ExpireTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expire:transactions';

    protected $description = 'Auto-expire Processing transactions older than 5 minutes';

    public function handle()
    {
        app(NewConsultationController::class)->expiredTransactions();
        $this->info('Expired transactions processed successfully.');
    }
}
