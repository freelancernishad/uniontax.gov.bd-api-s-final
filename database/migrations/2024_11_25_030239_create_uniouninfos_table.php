<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUniouninfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uniouninfos', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('short_name_e');
            $table->string('domain');
            $table->string('portal');
            $table->string('short_name_b');
            $table->string('thana');
            $table->string('district');
            $table->string('web_logo')->nullable();
            $table->string('sonod_logo')->nullable();
            $table->string('c_signture')->nullable();
            $table->string('c_name')->nullable();
            $table->string('c_type')->nullable();
            $table->string('c_email')->nullable();
            $table->string('socib_name')->nullable();
            $table->string('socib_signture')->nullable();
            $table->string('socib_email')->nullable();
            $table->string('format')->nullable();
            $table->string('u_image')->nullable();
            $table->text('u_description')->nullable();
            $table->text('u_notice')->nullable();
            $table->string('u_code')->unique();
            $table->string('contact_email')->nullable();
            $table->text('google_map')->nullable();
            $table->string('defaultColor')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('AKPAY_MER_REG_ID')->nullable();
            $table->string('AKPAY_MER_PASS_KEY')->nullable();
            $table->integer('smsBalance')->default(0);
            $table->boolean('nidServicestatus')->default(false);
            $table->text('nidService')->nullable();
            $table->boolean('status')->default(true);
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uniouninfos');
    }
}
