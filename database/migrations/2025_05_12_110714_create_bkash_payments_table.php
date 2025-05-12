<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bkash_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->unique();
            $table->string('id_token', 1000);
            $table->string('invoice')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('initiated'); // initiated, executed, failed, etc.
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bkash_payments');
    }
};
