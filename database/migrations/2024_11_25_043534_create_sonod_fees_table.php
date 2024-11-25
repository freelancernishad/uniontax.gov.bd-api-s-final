<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSonodfeesTable extends Migration
{
    public function up()
    {
        Schema::create('sonod_fees', function (Blueprint $table) {
            $table->id();
            $table->string('unioun');
            $table->integer('service_id'); // Regular integer column for service_id
            $table->decimal('fees', 10, 2);  // Assuming fees are monetary values
            $table->integer('sonodnamelist_id');  // Foreign key to Sonodnamelist
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sonod_fees');
    }
}
