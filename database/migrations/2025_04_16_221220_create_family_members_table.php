<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamilyMembersTable extends Migration
{
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('holding_id');
            $table->string('name');
            $table->string('relation');
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('nid_no')->nullable();
            $table->string('birth_certificate_no')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('occupation')->nullable();
            $table->string('education')->nullable();
            $table->boolean('disability')->default(false);
            $table->timestamps();

            $table->foreign('holding_id')->references('id')->on('holdingtaxes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
}
