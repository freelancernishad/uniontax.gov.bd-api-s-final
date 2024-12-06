<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHoldingtaxesTable extends Migration
{
    public function up()
    {
        Schema::create('holdingtaxes', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('unioun')->nullable();
            $table->string('holding_no')->nullable();
            $table->string('maliker_name')->nullable();
            $table->string('father_or_samir_name')->nullable();
            $table->string('gramer_name')->nullable();
            $table->string('word_no')->nullable();
            $table->string('nid_no')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('griher_barsikh_mullo')->nullable();
            $table->string('barsikh_muller_percent')->nullable();
            $table->string('jomir_vara')->nullable();
            $table->string('total_mullo')->nullable();
            $table->string('rokhona_bekhon_khoroch')->nullable();
            $table->string('prakklito_mullo')->nullable();
            $table->string('reyad')->nullable();
            $table->string('angsikh_prodoy_korjoggo_barsikh_mullo')->nullable();
            $table->string('barsikh_vara')->nullable();
            $table->string('rokhona_bekhon_khoroch_percent')->nullable();
            $table->string('prodey_korjoggo_barsikh_mullo')->nullable();
            $table->string('prodey_korjoggo_barsikh_varar_mullo')->nullable();
            $table->string('total_prodey_korjoggo_barsikh_mullo')->nullable();
            $table->string('current_year_kor')->nullable();
            $table->longText('bokeya')->nullable();
            $table->string('total_bokeya')->nullable();
            $table->string('image')->nullable();
            $table->string('busnessName')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('holdingtaxes');
    }
}
