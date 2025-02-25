<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummonsTable extends Migration {
    public function up() {
        Schema::create('summons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_court_case_id')->constrained()->onDelete('cascade');
            $table->string('summon_type')->nullable();
            $table->string('person_name')->nullable();
            $table->text('address')->nullable();
            $table->string('mobile')->nullable();
            $table->date('summon_date')->nullable();
            $table->string('summon_number')->unique();
            $table->string('delivery_status')->default('Pending');
            $table->string('union_name')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('summons');
    }
}
