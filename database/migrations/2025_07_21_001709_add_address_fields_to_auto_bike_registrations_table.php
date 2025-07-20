<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('auto_bike_registrations', function (Blueprint $table) {
            $table->string('current_division')->nullable()->after('application_id');
            $table->string('applicant_present_district')->nullable();
            $table->string('applicant_present_Upazila')->nullable();
            $table->string('applicant_present_union')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('applicant_permanent_division')->nullable();
            $table->string('applicant_permanent_district')->nullable();
            $table->string('applicant_permanent_Upazila')->nullable();
            $table->string('applicant_permanent_union')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('auto_bike_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'current_division',
                'applicant_present_district',
                'applicant_present_Upazila',
                'applicant_present_union',
                'permanent_address',
                'applicant_permanent_division',
                'applicant_permanent_district',
                'applicant_permanent_Upazila',
                'applicant_permanent_union',
            ]);
        });
    }
};
