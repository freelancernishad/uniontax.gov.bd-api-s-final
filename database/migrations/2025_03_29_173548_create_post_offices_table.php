<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('post_offices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unioninfo_id')->constrained('uniouninfos')->onDelete('cascade');
            $table->string('name_bn'); // পোষ্ট অফিস নাম বাংলা
            $table->string('name_en')->nullable(); // ইংরেজি নাম (ঐচ্ছিক)
            $table->string('post_code'); // পোষ্ট কোড
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('post_offices');
    }
};
