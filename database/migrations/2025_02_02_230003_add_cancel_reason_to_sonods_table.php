<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelReasonToSonodsTable extends Migration
{
    public function up()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->text('cancel_reason')->nullable()->after('cancedbyUserid'); // Adding cancel_reason column
        });
    }

    public function down()
    {
        Schema::table('sonods', function (Blueprint $table) {
            $table->dropColumn('cancel_reason'); // Dropping the cancel_reason column in case of rollback
        });
    }
}
