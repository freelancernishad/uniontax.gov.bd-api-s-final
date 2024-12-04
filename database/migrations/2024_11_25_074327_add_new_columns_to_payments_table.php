<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('union')->nullable()->after('payable_id');
            $table->string('trxId')->nullable()->after('union');
            $table->unsignedBigInteger('sonodId')->nullable()->after('trxId');
            $table->string('sonod_type')->nullable()->after('sonodId');
            $table->string('applicant_mobile')->nullable()->after('sonod_type');
            $table->date('date')->nullable()->after('applicant_mobile');
            $table->string('month')->nullable()->after('date');
            $table->year('year')->nullable()->after('month');
            $table->longText('paymentUrl')->nullable()->after('year');
            $table->json('ipnResponse')->nullable()->after('paymentUrl');
            $table->string('method')->nullable()->after('ipnResponse');
            $table->string('payment_type')->nullable()->after('method');
            $table->decimal('balance', 15, 2)->nullable()->after('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'union',
                'trxId',
                'sonodId',
                'sonod_type',
                'applicant_mobile',
                'date',
                'month',
                'year',
                'paymentUrl',
                'ipnResponse',
                'method',
                'payment_type',
                'balance',
            ]);
        });
    }
}
