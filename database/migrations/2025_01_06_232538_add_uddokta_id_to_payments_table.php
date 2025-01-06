<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUddoktaIdToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add the new column
            $table->unsignedBigInteger('uddoktaId')->nullable()->after('hasEnData');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the column if the migration is rolled back
            $table->dropColumn('uddoktaId');
        });
    }
}
