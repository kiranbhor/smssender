<?php

namespace Accunity\SMSSender\Jobs;

use Accunity\SMSSender\Models\SMSDetails;
use Accunity\SMSSender\SMSConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class UpdateDelivery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var SMSDetails
     */
    protected $smsDetails;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SMSDetails $smsDetails)
    {
        $this->smsDetails = $smsDetails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            if($this->smsDetails != null) {

                $response = SMSConfig::getResponse(trim($this->smsDetails->response_url));
                $this->smsDetails->delivery_status = $response->getBody()->getContents();


                if ($this->smsDetails->delivery_status == 'DELIVRD') {
                    $this->smsDetails->status = "D";
                }

                $this->smsDetails->save();
            }
        }
        catch (\Exception $ex){
            Log::error($ex);
        }
    }
}
