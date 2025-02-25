<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeesTable extends Migration {
    public function up() {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('village_court_cases')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_status')->default('Pending');
            $table->string('union_name')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('fees');
    }
}
