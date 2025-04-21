<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSohayotaBiboronsTable extends Migration
{
    public function up(): void
    {
        Schema::create('sohayota_biborons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('family_member_id');
            $table->string('sohayota_type');
            $table->string('card_number')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('family_member_id')->references('id')->on('family_members')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sohayota_biborons');
    }
}

