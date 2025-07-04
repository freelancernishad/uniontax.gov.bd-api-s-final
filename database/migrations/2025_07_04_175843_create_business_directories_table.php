<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessDirectoriesTable extends Migration
{
    public function up()
    {
        Schema::create('business_directories', function (Blueprint $table) {
            $table->id();

            $table->string('union_name')->nullable(); // ইউনিয়ন নাম

            $table->string('applicant_owner_type')->nullable(); // ব্যক্তি / প্রতিষ্ঠান
            $table->string('applicant_name_of_the_organization')->nullable();
            $table->string('organization_address')->nullable();
            $table->string('applicant_occupation')->nullable();
            $table->string('applicant_vat_id_number')->nullable();
            $table->string('applicant_tax_id_number')->nullable();

            $table->string('applicant_type_of_businessKhat')->nullable();
            $table->string('applicant_type_of_businessKhatAmount')->nullable();
            $table->string('last_years_money')->nullable();
            $table->string('applicant_type_of_business')->nullable();

            $table->string('name')->nullable(); // আবেদনকারীর নাম
            $table->enum('gender', ['পুরুষ', 'মহিলা', 'অন্যান্য'])->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('nid_no')->nullable();
            $table->string('birth_id_no')->nullable();
            $table->string('mobile_no')->nullable();

            $table->string('applicant_holding_tax_number')->nullable();
            $table->string('holding_owner_name')->nullable();
            $table->string('holding_owner_relationship')->nullable();
            $table->string('holding_owner_mobile')->nullable();

            $table->date('applicant_date_of_birth')->nullable();
            $table->string('applicant_religion')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('business_directories');
    }
}
