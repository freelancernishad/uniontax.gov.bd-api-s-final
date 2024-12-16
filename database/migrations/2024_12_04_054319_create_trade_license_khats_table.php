<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTradeLicenseKhatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_license_khats', function (Blueprint $table) {
            $table->id();
            $table->string('khat_id')->unique();  // Unique identifier for the Khat
            $table->string('name');               // Name of the Khat
            $table->unsignedBigInteger('main_khat_id')->nullable();  // Reference to the main Khat
            $table->string('type');               // Type of the Khat
            $table->timestamps();
        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_license_khats');
    }
}
