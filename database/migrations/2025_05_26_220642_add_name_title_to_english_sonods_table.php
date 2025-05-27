<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameTitleToEnglishSonodsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('english_sonods', function (Blueprint $table) {
            $table->string('name_title', 50)->nullable()->after('applicant_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('english_sonods', function (Blueprint $table) {
            $table->dropColumn('name_title');
        });
    }
}
