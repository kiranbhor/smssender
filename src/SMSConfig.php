<?php

namespace  Accunity\SMSSender;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SMSConfig
{
    public $username;
    public $password;
    public $senderId;
    public $smsUrl;
    public $extraUrlAppend;
    public $mobileNo;
    public $message;
    public $priority;
    public $type;
    public $generatedURL;

    public $userNamePlaceholder;
    public $passwordPlaceholder;
    public $senderIdPlaceholder;
    public $mobileNoPlaceholder;
    public $messagePlaceholder;
    public $priorityPlaceholder;
    public $typePlaceholder;



    public function __construct()
    {
        $this->username = Config::get('smssender.SMS_USERNAME');
        $this->password  = Config::get('smssender.SMS_PASSWORD');
        $this->senderId  = Config::get('smssender.SMS_SENDER_ID');
        $this->smsUrl = Config::get('smssender.SMS_URL');
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
    }

    public function getSMSUrl(){

        $this->generatedURL = $this->smsUrl;

        $this->generatedURL = str_replace($this->userNamePlaceholder,$this->username,$this->generatedURL);
        $this->generatedURL = str_replace($this->passwordPlaceholder,$this->password,$this->generatedURL);
        $this->generatedURL = str_replace($this->senderIdPlaceholder,$this->senderId,$this->generatedURL);
        $this->generatedURL = str_replace($this->mobileNoPlaceholder,$this->mobileNo,$this->generatedURL);
        $this->generatedURL = str_replace($this->messagePlaceholder,$this->message,$this->generatedURL);
        $this->generatedURL= str_replace($this->priorityPlaceholder,$this->priority,$this->generatedURL);
        $this->generatedURL = str_replace($this->typePlaceholder,$this->type,$this->generatedURL);
        $this->generatedURL = $this->generatedURL . $this->extraUrlAppend;

        return $this->generatedURL;
    }

    public function send(){

        try{

            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', $this->getSMSUrl());


            $response = [
                'status' => 'success',
                'code' => $res->getStatusCode(),
                'body' => $res->getBody(),
                'contents' => $res->getBody()->getContents()
            ];

            return $response;
        }
        catch (\Exception $ex){

            $response = [
                'status' => 'failure',
                'message' => $ex->getMessage(),
                'exception' => $ex
            ];
            return $response;
        }
    }

}
