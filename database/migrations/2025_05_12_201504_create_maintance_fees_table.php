<?php

// database/migrations/xxxx_xx_xx_create_maintance_fees_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintanceFeesTable extends Migration
{
    public function up(): void
    {
        Schema::create('maintance_fees', function (Blueprint $table) {
            $table->id();
            $table->string('union');
            $table->decimal('amount', 10, 2);
            $table->string('status')->nullable(); // e.g. paid, unpaid
            $table->date('payment_date')->nullable();
            $table->enum('type', ['monthly', 'yearly']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintance_fees');
    }
}
