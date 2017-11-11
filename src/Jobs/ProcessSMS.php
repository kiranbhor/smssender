<?php

namespace Accunity\SMSSender\Jobs;

use Accunity\SMSSender\SMSConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $smsConfig;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SMSConfig $smsConfig)
    {
        $this->smsConfig = $smsConfig;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $this->smsConfig->send();
        }
        catch (\Exception $ex){

            Log::error($ex);
        }

    }
}
