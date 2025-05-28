<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChalanFieldsToSonodsTable extends Migration
{
    public function up()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->string('chalan_traking_no', 60)->nullable()->after('signboard_size_square_fit');
            $table->date('chalan_date')->nullable()->after('chalan_traking_no');
            $table->decimal('chalan_amount', 10, 2)->nullable()->after('chalan_date');
        });
    }

    public function down()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->dropColumn(['chalan_traking_no', 'chalan_date', 'chalan_amount']);
        });
    }
}
