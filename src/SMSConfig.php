<?php

namespace  Accunity\SMSSender;

use Accunity\SMSSender\Jobs\ProcessSMS;
use Accunity\SMSSender\Jobs\UpdateDelivery;
use Accunity\SMSSender\Models\SMSDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Psr7\Request;

class SMSConfig 
{
    public $username;
    public $password;
    public $senderId;

    public $smsUrl;
    public $responseURL;
    public $balanceCheckUrl;

    public $extraUrlAppend;
    public $mobileNo;
    public $message;
    public $priority;
    public $type;
    public $generatedURL;
    public $deliverURL;

    public $userNamePlaceholder;
    public $passwordPlaceholder;
    public $senderIdPlaceholder;
    public $mobileNoPlaceholder;
    public $messagePlaceholder;
    public $priorityPlaceholder;
    public $typePlaceholder;
    public $messageIdPlaceholder;

    public $enableSMS;
    public $smsDetailsId;
    public $userid;



    public static $client;

    public static function getClient(){
        if(SMSConfig::$client == null){
            SMSConfig::$client = new \GuzzleHttp\Client();
        }
        return SMSConfig::$client;
    }


    public function __construct()
    {
        $this->username = Config::get('smssender.SMS_USERNAME');
        $this->password  = Config::get('smssender.SMS_PASSWORD');
        $this->senderId  = Config::get('smssender.SMS_SENDER_ID');

        $this->smsUrl = Config::get('smssender.SMS_URL');
        $this->responseURL = Config::get('smssender.RESPONSE_URL');
        $this->balanceCheckUrl = Config::get('smssender.BALANCE_CHECK_URL');

        $this->priority = Config::get('smssender.SMS_PRIORITY');
        $this->type = Config::get('smssender.SMS_TYPE');


        $this->extraUrlAppend = Config::get('smssender.SMS_EXTRA_URL_APPEND');
        $this->userNamePlaceholder = Config::get('smssender.SMS_URL_USERNAME_PLACEHOLDER');
        $this->passwordPlaceholder = Config::get('smssender.SMS_URL_PASSWORD_PLACEHOLDER');
        $this->senderIdPlaceholder = Config::get('smssender.SMS_URL_SENDER_ID_PLACEHOLDER');
        $this->mobileNoPlaceholder = Config::get('smssender.SMS_URL_MOBILE_NO_PLACEHOLDER');
        $this->messagePlaceholder = Config::get('smssender.SMS_URL_MESSAGE_PLACEHOLDER');
        $this->priorityPlaceholder = Config::get('smssender.SMS_URL_PRIORITY_PLACEHOLDER');
        $this->typePlaceholder = Config::get('smssender.SMS_URL_TYPE_PLACEHOLDER');
        $this->messageIdPlaceholder = Config::get('smssender.MESSAGE_ID_PLACEHOLDER');
        $this->enableSMS= Config::get('smssender.SMS_ENABLE');
    }



    public function getSMSUrl(){

        $this->generatedURL = $this->smsUrl;

        //Change newline charactor
        $this->message = str_replace("\n", "%0a",$this->message);
        $this->generatedURL = str_replace($this->userNamePlaceholder,$this->username,$this->generatedURL);
        $this->generatedURL = str_replace($this->passwordPlaceholder,$this->password,$this->generatedURL);
        $this->generatedURL = str_replace($this->senderIdPlaceholder,$this->senderId,$this->generatedURL);
        $this->generatedURL = str_replace($this->mobileNoPlaceholder,$this->mobileNo,$this->generatedURL);
        $this->generatedURL = str_replace($this->messagePlaceholder,$this->message,$this->generatedURL);
        $this->generatedURL=  str_replace($this->priorityPlaceholder,$this->priority,$this->generatedURL);
        $this->generatedURL = str_replace($this->typePlaceholder,$this->type,$this->generatedURL);
        $this->generatedURL = $this->generatedURL . $this->extraUrlAppend;
        return $this->generatedURL;

    }

    public function getResponseURL($messageId){

        $this->deliverURL = $this->responseURL;

        $this->deliverURL = str_replace($this->userNamePlaceholder,$this->username,$this->deliverURL);
        $this->deliverURL = str_replace($this->mobileNoPlaceholder,$this->mobileNo,$this->deliverURL);
        $this->deliverURL = str_replace($this->priorityPlaceholder,$this->priority,$this->deliverURL);
        $this->deliverURL = str_replace($this->messageIdPlaceholder,$messageId,$this->deliverURL);


        return $this->deliverURL;
    }

    public function getBalanceCheckUrl(){
        $this->deliverURL = $this->balanceCheckUrl;

        $this->deliverURL = str_replace($this->userNamePlaceholder,$this->username,$this->deliverURL);
        $this->generatedURL = str_replace($this->passwordPlaceholder,$this->password,$this->generatedURL);

        return $this->deliverURL;
    }

    public static function getResponse($url){
        return SMSConfig::getClient()->request('GET', trim($url));
    }


    public  function getSMSBalance(){

        $response =  SMSConfig::getResponse($this->getBalanceCheckUrl());
        return $response->getBody()->getContents();
    }

    public function sendAsync($userId = null){

        $this->userid = $userId;
        ProcessSMS::dispatch($this);
    }



    public function send(){
        try{

            if($this->enableSMS == false){
                return false;
            }

            $details = SMSDetails::where('date','=',Carbon::now()->format('Y-m-d'))
                ->where('contact_no','=',$this->mobileNo)
                ->where('text','=',$this->message)
                ->get();

            if(count($details) == 0) {

                    $smsDetails = SMSDetails::create([
                        'user_id' => $this->userid,
                        'date' => Carbon::now(),
                        'text' => $this->message,
                        'status' => 'P',
                        'response' => 'Not Sent',
                        'delivery_status' => '',
                        'response_code' => '',
                        'contact_no' => $this->mobileNo,
                        'priority' => $this->priority,
                        'sms_url' => $this->getSMSUrl(),
                        'sms_code'=>''

                    ]);

                    $this->smsDetailsId = $smsDetails->id;


                    $client = new \GuzzleHttp\Client();
                    $res = $client->request('GET', $this->getSMSUrl());

                    $response = [
                        'status' => 'success',
                        'code' => $res->getStatusCode(),
                        'body' => $res->getBody(),
                        'contents' => $res->getBody()->getContents()
                    ];


                    if ($smsDetails != null) {
                        $smsDetails->status = 'S';
                        $smsDetails->response = $res->getBody()->getContents();
                        $smsDetails->response_code = $res->getStatusCode();
                        $smsDetails->sms_code = $res->getBody();
                        $smsDetails->response_url = $this->getResponseURL($smsDetails->sms_code);
                        $smsDetails->save();

                        UpdateDelivery::dispatch($smsDetails)->delay(config('smssender.DELIVERY_CHECK_DELAY',60));
                    }

                return $response;
            }
            else{
                Log::info("Ignoring send SMS: " .$this->message . ' to :'.$this->mobileNo. ' for date: '.Carbon::now()->format('Y-m-d') . ' is already sent' );
            }


        }
        catch (\Exception $ex){

            $response = [
                'status' => 'failure',
                'message' => $ex->getMessage(),
                'exception' => $ex
            ];

            $smsDetails = SMSDetails::find($this->smsDetailsId);

            if($smsDetails != null){
                $smsDetails->status = 'F';
                $smsDetails->response = $ex->getMessage();
                $smsDetails->response_code = '' ;
                $smsDetails->delivery_status = '';
                $smsDetails->save();
            }

            return $response;
        }
    }

}
