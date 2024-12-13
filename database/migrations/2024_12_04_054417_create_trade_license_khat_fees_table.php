<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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

        });

        // Call the seeder to insert data after migration
        // Artisan::call('db:seed', [
        //     '--class' => 'TradeLicenseKhatFeeSeeder'
        // ]);
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
