<?php

// database/migrations/xxxx_xx_xx_create_purchase_sms_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseSmsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_sms', function (Blueprint $table) {
            $table->id();
            $table->string('union_name');
            $table->string('mobile');
            $table->string('trx_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->integer('sms_amount');
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_sms');
    }
}
