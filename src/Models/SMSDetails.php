<?php

namespace Accunity\SMSSender\Models;

use Illuminate\Database\Eloquent\Model;

class SMSDetails extends Model
{
    protected $table = "smssender_smsdetails";

    protected $fillable = [
        'user_id',
        'date',
        'text',
        'status',
        'response',
        'response_code',
        'sms_code',
        'contact_no',
        'priority',
        'sms_url',
        'response_url'
    ];
}