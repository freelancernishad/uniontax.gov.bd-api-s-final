<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSonodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sonods', function (Blueprint $table) {
            $table->id();
            $table->string('unioun_name', 50)->nullable();
            $table->year('year')->nullable();
            $table->string('sonod_Id', 30);
            $table->string('uniqeKey', 255)->unique();
            $table->string('image', 255)->nullable();
            $table->string('sonod_name', 70)->nullable();
            $table->string('successor_father_name', 150)->nullable();
            $table->string('successor_mother_name', 150)->nullable();
            $table->string('ut_father_name', 150)->nullable();
            $table->string('ut_mother_name', 255)->nullable();
            $table->string('ut_grame', 150)->nullable();
            $table->string('ut_post', 150)->nullable();
            $table->string('ut_thana', 150)->nullable();
            $table->string('ut_district', 150)->nullable();
            $table->string('ut_word', 150)->nullable();
            $table->boolean('successor_father_alive_status')->default(false);
            $table->boolean('successor_mother_alive_status')->default(false);
            $table->string('applicant_holding_tax_number', 50)->nullable();
            $table->string('applicant_national_id_number', 20)->nullable();
            $table->string('applicant_birth_certificate_number', 20)->nullable();
            $table->string('applicant_passport_number', 20)->nullable();
            $table->date('applicant_date_of_birth')->nullable();
            $table->string('family_name', 100)->nullable();
            $table->decimal('Annual_income', 10, 2)->nullable();
            $table->text('Annual_income_text')->nullable(); // Changed to TEXT
            $table->string('Subject_to_permission', 100)->nullable();
            $table->boolean('disabled')->default(false);
            $table->string('The_subject_of_the_certificate', 100)->nullable();
            $table->string('Name_of_the_transferred_area', 150)->nullable();
            $table->string('applicant_second_name', 150)->nullable();
            $table->string('applicant_owner_type', 150)->nullable();
            $table->string('applicant_name_of_the_organization', 255)->nullable();
            $table->string('organization_address', 255)->nullable();
            $table->string('applicant_name', 150)->nullable();
            $table->string('utname', 150)->nullable();
            $table->string('ut_religion', 50)->nullable();
            $table->boolean('alive_status')->default(false);
            $table->string('applicant_gender', 30)->nullable();
            $table->string('applicant_marriage_status', 30)->nullable();
            $table->string('applicant_vat_id_number', 30)->nullable();
            $table->string('applicant_tax_id_number', 30)->nullable();
            $table->string('applicant_type_of_business', 100)->nullable();
            $table->string('applicant_type_of_businessKhat', 20)->nullable();
            $table->string('applicant_type_of_businessKhatAmount', 20)->nullable();
            $table->string('applicant_father_name', 150)->nullable();
            $table->string('applicant_mother_name', 150)->nullable();
            $table->string('applicant_occupation', 50)->nullable();
            $table->string('applicant_education', 100)->nullable();
            $table->string('applicant_religion', 50)->nullable();
            $table->string('applicant_resident_status', 20)->nullable();
            $table->text('applicant_present_village')->nullable(); // Changed to TEXT
            $table->text('applicant_present_road_block_sector')->nullable(); // Changed to TEXT
            $table->string('applicant_present_word_number', 20)->nullable();
            $table->string('applicant_present_district', 50)->nullable();
            $table->string('applicant_present_Upazila', 50)->nullable();
            $table->string('applicant_present_post_office', 50)->nullable();
            $table->text('applicant_permanent_village')->nullable(); // Changed to TEXT
            $table->text('applicant_permanent_road_block_sector')->nullable(); // Changed to TEXT
            $table->string('applicant_permanent_word_number', 20)->nullable();
            $table->string('applicant_permanent_district', 50)->nullable();
            $table->string('applicant_permanent_Upazila', 50)->nullable();
            $table->string('applicant_permanent_post_office', 50)->nullable();
            $table->longText('successor_list')->nullable(); // Changed to TEXT
            $table->string('khat', 50)->nullable();
            $table->decimal('last_years_money', 10, 2)->nullable();
            $table->decimal('currently_paid_money', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->longText('amount_deails')->nullable(); // Changed to TEXT
            $table->text('the_amount_of_money_in_words')->nullable(); // Changed to TEXT
            $table->string('applicant_mobile', 30)->nullable();
            $table->string('applicant_email', 50)->nullable();
            $table->string('applicant_phone', 30)->nullable();
            $table->string('applicant_national_id_front_attachment', 255)->nullable();
            $table->string('applicant_national_id_back_attachment', 255)->nullable();
            $table->string('applicant_birth_certificate_attachment', 255)->nullable();
            $table->longText('prottoyon')->nullable();
            $table->longText('sec_prottoyon')->nullable();
            $table->string('stutus', 30)->nullable();
            $table->string('payment_status', 20)->nullable();
            $table->string('chaireman_name', 150)->nullable();
            $table->string('chaireman_type', 100)->nullable();
            $table->string('c_email', 50)->nullable();
            $table->string('chaireman_sign', 255)->nullable();
            $table->string('socib_name', 150)->nullable();
            $table->string('socib_signture', 255)->nullable();
            $table->string('socib_email', 50)->nullable();
            $table->string('cancedby', 100)->nullable();
            $table->unsignedBigInteger('cancedbyUserid')->nullable();
            $table->string('pBy', 100)->nullable();
            $table->boolean('sameNameNew')->default(false);
            $table->string('orthoBchor', 50)->nullable();
            $table->boolean('renewed')->default(false);
            $table->unsignedBigInteger('renewed_id')->nullable();
            $table->string('format', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sonods');
    }
}
