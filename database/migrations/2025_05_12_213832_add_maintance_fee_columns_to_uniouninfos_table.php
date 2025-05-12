<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->decimal('maintance_fee', 10, 2)->default(10000)->nullable()->after('user_phone');
            $table->enum('maintance_fee_type', ['monthly', 'yearly','Free Trial'])->default('yearly')->nullable()->after('maintance_fee');
        });
    }

    public function down(): void
    {
        Schema::table('uniouninfos', function (Blueprint $table) {
            $table->dropColumn(['maintance_fee', 'maintance_fee_type']);
        });
    }
};
