<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSonodnamelistsTable extends Migration
{
    public function up()
    {
        Schema::create('sonodnamelists', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id');  // Regular integer column for service_id
            $table->string('bnname');
            $table->string('enname');
            $table->string('icon')->nullable();
            $table->text('template');
            $table->integer('sonod_fee');  // Regular integer column for sonod_fee
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sonodnamelists');
    }
}
