<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionFeeToMaintanceFeesTable extends Migration
{
    public function up()
    {
        Schema::table('maintance_fees', function (Blueprint $table) {
            $table->decimal('transaction_fee', 10, 2)->default(0.00)->after('amount');
        });
    }

    public function down()
    {
        Schema::table('maintance_fees', function (Blueprint $table) {
            $table->dropColumn('transaction_fee');
        });
    }
}
