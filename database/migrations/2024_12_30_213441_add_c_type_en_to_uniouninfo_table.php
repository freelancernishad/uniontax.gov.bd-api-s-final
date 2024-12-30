<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCTypeEnToUniouninfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniouninfo', function (Blueprint $table) {
            $table->string('c_type_en')->nullable()->after('c_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uniouninfo', function (Blueprint $table) {
            $table->dropColumn('c_type_en');
        });
    }
}
