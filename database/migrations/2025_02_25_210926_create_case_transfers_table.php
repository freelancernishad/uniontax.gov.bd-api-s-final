<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaseTransfersTable extends Migration {
    public function up() {
        Schema::create('case_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('village_court_cases')->onDelete('cascade');
            $table->string('transfer_reason')->nullable();
            $table->date('transfer_date')->nullable();
            $table->string('union_name')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('case_transfers');
    }
}
