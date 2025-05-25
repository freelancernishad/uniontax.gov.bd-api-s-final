<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSignboardColumnsToSonodsTable extends Migration
{
    public function up()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->string('signboard_type')->nullable()->after('font_family');
            $table->string('signboard_size_square_fit')->nullable()->after('signboard_type');
        });
    }

    public function down()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->dropColumn('signboard_type');
            $table->dropColumn('signboard_size_square_fit');
        });
    }
}

