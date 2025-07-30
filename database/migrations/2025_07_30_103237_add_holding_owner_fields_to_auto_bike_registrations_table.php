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
        Schema::table('auto_bike_registrations', function (Blueprint $table) {
            $table->string('holding_owner_name')->nullable();
            $table->string('holding_owner_relationship')->nullable();
            $table->string('holding_owner_mobile')->nullable();
        });
    }

    public function down()
    {
        Schema::table('auto_bike_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'holding_owner_name',
                'holding_owner_relationship',
                'holding_owner_mobile',
            ]);
        });
    }

};
