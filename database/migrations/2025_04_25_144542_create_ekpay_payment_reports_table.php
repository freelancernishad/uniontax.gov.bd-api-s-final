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
        Schema::create('ekpay_payment_reports', function (Blueprint $table) {
            $table->id();
            $table->string('union');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('ekpay_amount', 12, 2);
            $table->decimal('server_amount', 12, 2);
            $table->decimal('difference_amount', 12, 2)->comment('Positive or negative difference between ekpay and server amount');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekpay_payment_reports');
    }
};
