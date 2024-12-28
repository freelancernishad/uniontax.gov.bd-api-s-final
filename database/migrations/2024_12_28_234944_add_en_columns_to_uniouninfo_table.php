<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnColumnsToUniouninfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->string('full_name_en')->nullable()->after('full_name');
            $table->string('c_name_en')->nullable()->after('c_name');
            $table->string('district_en')->nullable()->after('district');
            $table->string('thana_en')->nullable()->after('thana');
            $table->string('socib_name_en')->nullable()->after('socib_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->dropColumn('full_name_en');
            $table->dropColumn('c_name_en');
            $table->dropColumn('district_en');
            $table->dropColumn('thana_en');
            $table->dropColumn('socib_name_en');
        });
    }
}
