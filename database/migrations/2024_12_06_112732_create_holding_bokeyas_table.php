<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHoldingbokeyasTable extends Migration
{
    public function up()
    {
        Schema::create('holding_bokeyas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('holdingTax_id')->nullable()->constrained('holdingtaxes');
            $table->string('year')->nullable();
            $table->string('price')->nullable();
            $table->string('payYear')->nullable();
            $table->string('payOB')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('holding_bokeyas');
    }
}
