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
    Schema::table('maintance_fees', function (Blueprint $table) {
        $table->string('trx_id')->nullable()->after('type');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintance_fees', function (Blueprint $table) {
            $table->dropColumn(['trx_id']);
        });
    }
};
