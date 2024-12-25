<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentFailedTable extends Migration
{
    public function up()
    {
        Schema::create('payment_faileds', function (Blueprint $table) {
            $table->id();
            $table->string('union_name');
            $table->string('certificate');
            $table->string('payment_method');
            $table->string('account_number');
            $table->decimal('amount', 10, 2);
            $table->string('transaction_id');
            $table->string('sonod_id');
            $table->text('details')->nullable();
            $table->string('transId');
            $table->enum('status', ['Pending', 'ekpay_submited', 'Completed'])->default('Pending'); // Enum for status
            $table->text('comment')->nullable();
            $table->timestamp('datetime')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_faileds');
    }
}
