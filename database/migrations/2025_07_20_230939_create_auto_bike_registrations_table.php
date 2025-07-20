<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoBikeRegistrationsTable extends Migration
{
    public function up()
    {
        Schema::create('auto_bike_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('union_name');


            // Application info
            $table->string('fiscal_year');
            $table->string('application_type');

            // Applicant info
            $table->string('applicant_name_bn');
            $table->string('applicant_name_en');
            $table->string('applicant_father_name');
            $table->string('applicant_mother_name');
            $table->string('applicant_gender');
            $table->string('nationality');
            $table->string('applicant_religion');
            $table->date('applicant_date_of_birth');
            $table->string('marital_status');
            $table->string('profession')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('applicant_mobile');

            // Emergency contact
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->string('emergency_contact_relation');
            $table->string('emergency_contact_national_id_number');

            // Auto-bike info
            $table->date('auto_bike_purchase_date');
            $table->date('auto_bike_last_renew_date')->nullable();
            $table->string('auto_bike_supplier_name');
            $table->text('auto_bike_supplier_address');
            $table->string('auto_bike_supplier_mobile');

            // Documents (store file paths)
            $table->string('passport_photo')->nullable();
            $table->string('national_id_copy')->nullable();
            $table->string('auto_bike_receipt')->nullable();
            $table->string('previous_license_copy')->nullable();
            $table->string('affidavit_copy')->nullable();
            $table->string('status')->default('pending'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('auto_bike_registrations');
    }
}
