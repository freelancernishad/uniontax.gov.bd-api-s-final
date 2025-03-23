<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('purchase_sms', function (Blueprint $table) {
            $table->string('bank_trx_id')->nullable()->after('trx_id');
            $table->string('method')->nullable()->after('bank_trx_id');
        });
    }

    public function down()
    {
        Schema::table('purchase_sms', function (Blueprint $table) {
            $table->dropColumn(['bank_trx_id', 'method']);
        });
    }
};
