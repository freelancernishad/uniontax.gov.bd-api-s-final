<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSonodHoldingOwnersTable extends Migration
{
    public function up()
    {
        Schema::create('sonod_holding_owners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sonod_id')->index(); // Reference to Sonod
            $table->string('holding_no')->nullable();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('relationship')->nullable(); // e.g., নিজ, ভাই, ছেলে
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sonod_holding_owners');
    }
}

