<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSonodnamelistsTable extends Migration
{
    public function up()
    {
        Schema::create('sonodnamelists', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id');  // Regular integer column for service_id
            $table->string('bnname');
            $table->string('enname');
            $table->string('icon')->nullable();
            $table->text('template');
            $table->integer('sonod_fee');  // Regular integer column for sonod_fee
            $table->timestamps();
        });


                // Call the seeder to insert data after migration
                Artisan::call('db:seed', [
                    '--class' => 'SonodnamelistSeeder'
                ]);


    }

    public function down()
    {
        Schema::dropIfExists('sonodnamelists');
    }
}
