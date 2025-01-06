<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUddoktaSearchesTable extends Migration
{
    public function up()
    {
        Schema::create('uddokta_searches', function (Blueprint $table) {
            $table->id();
            $table->string('sonod_name');
            $table->string('nid_number');
            $table->unsignedBigInteger('uddokta_id');
            $table->json('api_response')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('uddokta_id')->references('id')->on('uddoktas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('uddokta_searches');
    }
}
