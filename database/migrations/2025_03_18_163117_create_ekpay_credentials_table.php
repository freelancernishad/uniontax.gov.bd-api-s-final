<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('ekpay_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('merchant_id')->nullable();
            $table->string('mer_pas_key')->nullable();
            $table->string('api_key')->nullable();
            $table->string('base_url')->nullable();
            $table->string('callback_url')->nullable();
            $table->string('whitelistip')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ekpay_credentials');
    }
};
