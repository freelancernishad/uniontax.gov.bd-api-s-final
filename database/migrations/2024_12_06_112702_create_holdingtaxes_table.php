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
            $table->string('category');
            $table->string('unioun');
            $table->string('holding_no');
            $table->string('maliker_name');
            $table->string('father_or_samir_name');
            $table->string('gramer_name');
            $table->string('word_no');
            $table->string('nid_no');
            $table->string('mobile_no');
            $table->decimal('griher_barsikh_mullo', 15, 2);
            $table->decimal('barsikh_muller_percent', 5, 2);
            $table->decimal('jomir_vara', 15, 2);
            $table->decimal('total_mullo', 15, 2);
            $table->decimal('rokhona_bekhon_khoroch', 15, 2);
            $table->decimal('prakklito_mullo', 15, 2);
            $table->decimal('reyad', 15, 2);
            $table->decimal('angsikh_prodoy_korjoggo_barsikh_mullo', 15, 2);
            $table->decimal('barsikh_vara', 15, 2);
            $table->decimal('rokhona_bekhon_khoroch_percent', 5, 2);
            $table->decimal('prodey_korjoggo_barsikh_mullo', 15, 2);
            $table->decimal('prodey_korjoggo_barsikh_varar_mullo', 15, 2);
            $table->decimal('total_prodey_korjoggo_barsikh_mullo', 15, 2);
            $table->decimal('current_year_kor', 15, 2);
            $table->decimal('bokeya', 15, 2);
            $table->decimal('total_bokeya', 15, 2);
            $table->string('image');
            $table->string('busnessName');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('holdingtaxes');
    }
}
