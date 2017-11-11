<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Messageetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master__smsdetails', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->date('date')->nullable();
            $table->text('text')->nullable();
            $table->string('status')->nullable();
            $table->text('response')->nullable();
            $table->string('delivery_status')->nullable();
            $table->string('response_code')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('priority')->nullable();
            $table->text('sms_url')->nullable();
            $table->text('response_url')->nullable();
            $table->string('sms_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master__smsdetails');
    }
}
