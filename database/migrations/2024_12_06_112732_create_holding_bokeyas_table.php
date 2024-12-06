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
            $table->foreignId('holdingTax_id')->constrained('holdingtaxes');
            $table->integer('year');
            $table->decimal('price', 15, 2);
            $table->integer('payYear');
            $table->decimal('payOB', 15, 2);
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('holding_bokeyas');
    }
}
