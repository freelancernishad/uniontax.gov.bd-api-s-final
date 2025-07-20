<?php

// create a new migration or update existing one
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApplicationIdToAutoBikeRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::table('auto_bike_registrations', function (Blueprint $table) {
            $table->string('application_id')->unique()->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('auto_bike_registrations', function (Blueprint $table) {
            $table->dropColumn('application_id');
        });
    }
}
