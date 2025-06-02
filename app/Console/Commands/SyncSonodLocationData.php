<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sonod;
use App\Models\Uniouninfo;

class SyncSonodLocationData extends Command
{

    // php artisan sync:sonod-location
    protected $signature = 'sync:sonod-location';

    protected $description = 'Sync Sonod records with division, district, and upazila from unioninfos';

  public function handle()
{
    $this->info('Starting sync of Sonod location data...');

    Uniouninfo::query()->chunk(100, function ($unions) {
        foreach ($unions as $union) {
            $unionNameKey = strtolower(str_replace(' ', '', $union->short_name_e));

            $updated = Sonod::where('unioun_name', $unionNameKey)
                ->update([
                    'division_name' => $union->division_name,
                    'district_name' => $union->district_name,
                    'upazila_name' => $union->upazila_name,
                ]);

            $this->info("Updated {$updated} sonod records for union: {$union->short_name_e}");
        }
    });

    $this->info('Sync completed.');
}

}
