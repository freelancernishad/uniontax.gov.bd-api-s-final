<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSonodFilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sonod_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sonod_id');
            $table->string('type'); // সুপারিশ, প্রত্যয়ন, etc.
            $table->string('file_path'); // path to the uploaded file
            $table->timestamps();

            $table->foreign('sonod_id')->references('id')->on('sonods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sonod_files');
    }
}
