<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            // $table->string('phone')->nullable()->after('email');
            // $table->string('position')->nullable()->after('phone');
            // $table->string('full_unioun_name')->nullable()->after('position');
            // $table->string('gram')->nullable()->after('full_unioun_name');
            // $table->string('district')->nullable()->after('gram');
            // $table->string('thana')->nullable()->after('district');
            // $table->string('word')->nullable()->after('thana');
            // $table->text('description')->nullable()->after('word');
            // $table->string('image')->nullable()->after('description');
            // $table->boolean('status')->default(true)->after('image');
            // $table->string('role')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'unioun',
                'phone',
                'position',
                'full_unioun_name',
                'gram',
                'district',
                'thana',
                'word',
                'description',
                'image',
                'status',
                'role',
            ]);
        });
    }
}
