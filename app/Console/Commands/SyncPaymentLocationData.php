<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Uniouninfo;

class SyncPaymentLocationData extends Command
{

    // php artisan sync:payment-location


    protected $signature = 'sync:payment-location';

    protected $description = 'Sync Payment records with division, district, and upazila from unioninfos';

    public function handle()
    {
        $this->info('Starting sync of Payment location data...');

        Uniouninfo::query()->chunk(100, function ($unions) {
            foreach ($unions as $union) {
                // Normalize the union name for comparison
                $unionNameKey = strtolower(str_replace(' ', '', $union->short_name_e));

                $updated = Payment::whereRaw("REPLACE(LOWER(`union`), ' ', '') = ?", [$unionNameKey])
                    ->update([
                        'division_name' => $union->division_name,
                        'district_name' => $union->district_name,
                        'upazila_name' => $union->upazila_name,
                    ]);

                $this->info("Updated {$updated} payment records for union: {$union->short_name_e}");
            }
        });

        $this->info('Sync completed.');
    }
}
