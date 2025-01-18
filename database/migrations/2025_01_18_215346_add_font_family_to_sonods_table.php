<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFontFamilyToSonodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->string('font_family')->default('bangla')->after('hasEnData');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->dropColumn('font_family');
        });
    }
}
