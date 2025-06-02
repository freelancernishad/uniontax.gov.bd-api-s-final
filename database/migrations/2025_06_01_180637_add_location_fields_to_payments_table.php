<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationFieldsToPaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('division_name')->nullable()->after('union');
            $table->string('district_name')->nullable()->after('division_name');
            $table->string('upazila_name')->nullable()->after('district_name');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['division_name', 'district_name', 'upazila_name']);
        });
    }
}
