<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uddoktas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('position')->nullable();
            $table->string('division_name')->nullable();
            $table->string('district_name')->nullable();
            $table->string('upazila_name')->nullable();
            $table->string('union_name')->nullable();
            $table->timestamp('email_verified_at')->nullable(); // For email verification
            $table->string('email_verification_hash')->nullable(); // For email verification link
            $table->string('otp')->nullable(); // For OTP verification
            $table->timestamp('otp_expires_at')->nullable(); // For OTP expiration
            $table->rememberToken(); // For "remember me" functionality
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uddoktas');
    }
};
