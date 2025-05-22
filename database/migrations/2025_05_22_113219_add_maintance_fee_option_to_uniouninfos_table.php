<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaintanceFeeOptionToUniouninfosTable extends Migration
{
    public function up()
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->enum('maintance_fee_option', ['required', 'optional'])
              ->default('optional')
              ->nullable()
              ->after('maintance_fee_type');
        });
    }

    public function down()
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->dropColumn('maintance_fee_option');
        });
    }
}
