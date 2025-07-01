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
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->unsignedTinyInteger('maintance_fee_notice_date')->default(15)->after('maintance_fee_payment_last_date');
        });
    }

    public function down()
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->dropColumn('maintance_fee_notice_date');
        });
    }

};
