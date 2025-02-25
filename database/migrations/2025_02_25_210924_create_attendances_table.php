<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration {
    public function up() {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('village_court_cases')->onDelete('cascade');
            $table->string('person_name')->nullable();
            $table->string('role')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('attendances');
    }
}
