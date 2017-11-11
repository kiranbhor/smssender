<?php

namespace Accunity\SMSSender\Commands;

use Accunity\SMSSender\Jobs\UpdateDelivery;
use Accunity\SMSSender\Models\SMSDetails;
use Accunity\SMSSender\SMSConfig;
use Illuminate\Console\Command;

class UpdateSMSStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:updatestatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetched delivery status for all the pending sms';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pendingSMS = SMSDetails::whereIn('status',['P','S'])->get();

        foreach ($pendingSMS as $sms){
            UpdateDelivery::dispatch($sms);
        }

    }
}
