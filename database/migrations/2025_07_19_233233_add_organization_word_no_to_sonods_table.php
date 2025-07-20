<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrganizationWordNoToSonodsTable extends Migration
{
    public function up()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->string('organization_word_no',20)->nullable()->after('organization_address');
        });
    }

    public function down()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->dropColumn('organization_word_no');
        });
    }
}
