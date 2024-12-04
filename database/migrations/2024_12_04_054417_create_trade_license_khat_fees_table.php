<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeLicenseKhatFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_license_khat_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('khat_fee_id')->nullable();  // Optional reference for grouping fees
            $table->string('khat_id_1');  // Primary Khat reference (from khat_id in trade_license_khats)
            $table->string('khat_id_2')->nullable();  // Secondary Khat reference (optional)
            $table->decimal('fee', 10, 2);  // Fee amount
            $table->timestamps();

            // Foreign key constraints to reference khat_id in trade_license_khats
            $table->foreign('khat_id_1')->references('khat_id')->on('trade_license_khats')->onDelete('cascade');
            $table->foreign('khat_id_2')->references('khat_id')->on('trade_license_khats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_license_khat_fees');
    }
}
