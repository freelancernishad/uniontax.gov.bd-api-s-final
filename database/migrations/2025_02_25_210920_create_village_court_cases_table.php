<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// VillageCourtCase Migration
class CreateVillageCourtCasesTable extends Migration {
    public function up() {
        Schema::create('village_court_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->string('applicant_name')->nullable();
            $table->string('applicant_father_husband_name')->nullable();
            $table->text('applicant_address')->nullable();
            $table->string('applicant_mobile')->nullable();
            $table->string('defendant_name')->nullable();
            $table->string('defendant_father_husband_name')->nullable();
            $table->text('defendant_address')->nullable();
            $table->string('defendant_mobile')->nullable();
            $table->string('case_type')->nullable();
            $table->text('case_details')->nullable();
            $table->date('application_date')->nullable();
            $table->string('case_status')->default('Pending');
            $table->string('case_register_number')->nullable();
            $table->text('order_sheet_details')->nullable();
            $table->string('union_name')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('village_court_cases');
    }
}
