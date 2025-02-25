<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNominationsTable extends Migration {
    public function up() {
        Schema::create('nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('village_court_cases')->onDelete('cascade');
            $table->string('member_name')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('union_name')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('nominations');
    }
}
