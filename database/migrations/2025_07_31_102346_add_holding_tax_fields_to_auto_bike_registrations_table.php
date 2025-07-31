<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHoldingTaxFieldsToAutoBikeRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::table('auto_bike_registrations', function (Blueprint $table) {
            $table->string('applicant_holding_tax_number')->nullable()->after('auto_bike_last_regi_no');
            $table->string('holding_tax_promanok')->nullable()->after('applicant_holding_tax_number');
        });
    }

    public function down()
    {
        Schema::table('auto_bike_registrations', function (Blueprint $table) {
            $table->dropColumn('applicant_holding_tax_number');
            $table->dropColumn('holding_tax_promanok');
        });
    }
}
