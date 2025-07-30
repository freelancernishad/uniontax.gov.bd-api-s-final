<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoBikeLastRegiNoToAutoBikeRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::table('auto_bike_registrations', function (Blueprint $table) {
            $table->string('auto_bike_last_regi_no')->nullable()->after('auto_bike_last_renew_date');
        });
    }

    public function down()
    {
        Schema::table('auto_bike_registrations', function (Blueprint $table) {
            $table->dropColumn('auto_bike_last_regi_no');
        });
    }
}
