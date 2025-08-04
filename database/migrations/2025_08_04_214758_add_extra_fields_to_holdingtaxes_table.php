<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToHoldingtaxesTable extends Migration
{
    public function up()
    {
        Schema::table('holdingtaxes', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('busnessName');
            $table->string('mother_name')->nullable()->after('date_of_birth');
            $table->string('profession')->nullable()->after('mother_name');
            $table->string('religion')->nullable()->after('profession');
            $table->string('house_type')->nullable()->after('religion');
            $table->string('social_facility')->nullable()->after('house_type');
            $table->string('sanitary_condition')->nullable()->after('social_facility');
            $table->integer('number_of_sons')->nullable()->after('sanitary_condition');
            $table->integer('number_of_daughters')->nullable()->after('number_of_sons');
            $table->string('house_loan')->nullable()->after('number_of_daughters');
            $table->decimal('land_amount', 10, 2)->nullable()->after('house_loan');
            $table->decimal('homestead_amount', 10, 2)->nullable()->after('land_amount');
            $table->decimal('business_capital', 12, 2)->nullable()->after('homestead_amount');
            $table->string('socioeconomic_status')->nullable()->after('business_capital');
        });
    }

    public function down()
    {
        Schema::table('holdingtaxes', function (Blueprint $table) {
            $table->dropColumn([
            'date_of_birth',
            'mother_name',
            'profession',
            'religion',
            'house_type',
            'social_facility',
            'sanitary_condition',
            'number_of_sons',
            'number_of_daughters',
            'house_loan',
            'land_amount',
            'homestead_amount',
            'business_capital',
            'socioeconomic_status',
            ]);
        });
    }
}
