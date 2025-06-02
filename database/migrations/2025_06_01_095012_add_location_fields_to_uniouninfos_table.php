<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationFieldsToUniouninfosTable extends Migration
{
    public function up()
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->string('division_name')->nullable()->after('short_name_e');
            $table->string('district_name')->nullable()->after('division_name');
            $table->string('upazila_name')->nullable()->after('district_name');
        });
    }

    public function down()
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->dropColumn(['division_name', 'district_name', 'upazila_name']);
        });
    }
}
