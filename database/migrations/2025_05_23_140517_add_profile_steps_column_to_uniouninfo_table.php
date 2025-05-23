<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileStepsColumnToUniouninfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            // Add a single column to track profile completion in steps (1-5, or custom)
            $table->integer('profile_steps')->default(0); // default is 0 (not started)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            // Drop the profile_steps column if rolling back the migration
            $table->dropColumn('profile_steps');
        });
    }
}
