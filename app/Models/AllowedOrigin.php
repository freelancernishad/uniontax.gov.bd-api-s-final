<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Devfaysal\BangladeshGeocode\Models\District;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AllowedOrigin extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'allowed_origins';

    // Define the fillable fields
    protected $fillable = ['origin_url'];

    /**
     * Get union names with appended domain and store them in the allowed_origins table.
     *
     * @param string $district
     * @return void
     */
    public function storeUnionNamesWithDomain($district)
    {
        // Get the district model
        $districtModel = District::where('name', $district)->firstOrFail();

        // Get all the unions from the upazilas of this district
        $unionNames = [];
        foreach ($districtModel->upazilas as $upazila) {
            foreach ($upazila->unions as $union) {
                $unionNames[] = str_replace(' ', '', strtolower($union->name));
            }
        }

        // Remove duplicates
        $unionNames = array_unique($unionNames);

        // Append '*.uniontax.gov.bd' to each union name
        $unionNamesWithDomain = array_map(function ($unionName) {
            return $unionName . '.uniontax.gov.bd';
        }, $unionNames);

        // Store the modified union names in the allowed_origins table
        foreach ($unionNamesWithDomain as $originUrl) {

            AllowedOrigin::create(['origin_url' => "https://$originUrl"]);

        }
    }
}
