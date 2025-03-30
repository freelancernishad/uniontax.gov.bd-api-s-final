<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unioninfo_id')->constrained('uniouninfos')->onDelete('cascade');
            $table->string('name_bn'); // গ্রাম/মহল্লা নাম বাংলা
            $table->string('name_en')->nullable(); // ইংরেজি নাম (ঐচ্ছিক)
            $table->integer('word_no'); // ওয়ার্ড নাম্বার
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('villages');
    }
};
