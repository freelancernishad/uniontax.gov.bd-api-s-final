<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationColumnsToSonodsTable extends Migration
{
    public function up()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->string('division_name', 25)->after('unioun_name');
            $table->string('district_name', 25)->after('division_name');
            $table->string('upazila_name', 25)->after('district_name');
        });
    }

    public function down()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->dropColumn(['division_name', 'district_name', 'upazila_name']);
        });
    }
}
