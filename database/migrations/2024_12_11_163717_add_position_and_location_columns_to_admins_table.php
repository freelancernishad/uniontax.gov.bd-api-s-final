<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('position')->nullable()->after('email');
            $table->string('division_name')->nullable()->after('position');
            $table->string('district_name')->nullable()->after('division_name');
            $table->string('upazila_name')->nullable()->after('district_name');
            $table->string('union_name')->nullable()->after('upazila_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['position', 'division_name', 'district_name', 'upazila_name', 'union_name']);
        });
    }
};
