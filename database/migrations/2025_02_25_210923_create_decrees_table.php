<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDecreesTable extends Migration {
    public function up() {
        Schema::create('decrees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('village_court_cases')->onDelete('cascade');
            $table->text('decree_details')->nullable();
            $table->string('issued_by')->nullable();
            $table->date('date_issued')->nullable();
            $table->string('union_name')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('decrees');
    }
}

