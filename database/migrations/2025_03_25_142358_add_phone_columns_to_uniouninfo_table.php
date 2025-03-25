<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->string('chairman_phone')->nullable();
            $table->string('secretary_phone')->nullable();
            $table->string('udc_phone')->nullable();
            $table->string('user_phone')->nullable();
        });
    }

    public function down()
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->dropColumn(['chairman_phone', 'secretary_phone', 'udc_phone', 'user_phone']);
        });
    }
};
