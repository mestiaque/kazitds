<?php

namespace ME\Kazitds\Http\Commands;

use App\Models\District;
use App\Models\Division;
use App\Models\Upazilla;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SyncGeoData extends Command
{
    protected $signature = 'geo-data:sync';
    protected $description = 'Sync geo-data from geo-data.json file';

    public function handle()
    {
        // Check if geo-data.json exists in public path
        if (!file_exists(public_path('geo-data.json'))) {
            $this->error('geo-data.json file not found in public directory!');
            return 1;
        }

        $json = file_get_contents(public_path('geo-data.json'));
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON format in geo-data.json!');
            return 1;
        }

        foreach ($data['divisions'] as $div) {
            // First check if division exists by ID
            $division = Division::find($div['id']);

            // If not found by ID, try to find by name to prevent duplicates
            if (!$division) {
                $division = Division::where('name', $div['name'])->orWhere('bn_name', $div['bn_name'])->first();
            }

            if (!$division) {
                $division = Division::create([
                    'id' => $div['id'],
                    'name' => $div['name'],
                    'bn_name' => $div['bn_name'],
                    'lat' => $div['lat'],
                    'long' => $div['long'],
                ]);
            } else {
                if (
                    $division->name !== $div['name'] ||
                    $division->bn_name !== $div['bn_name'] ||
                    $division->lat != $div['lat'] ||
                    $division->long != $div['long']
                ) {
                    $division->update([
                        'name' => $div['name'],
                        'bn_name' => $div['bn_name'],
                        'lat' => $div['lat'],
                        'long' => $div['long'],
                    ]);
                }
            }

            foreach ($div['districts'] as $dist) {
                // First check if district exists by ID
                $district = District::find($dist['id']);

                // If not found by ID, try to find by name to prevent duplicates
                if (!$district) {
                    $district = District::where('division_id', $division->id)
                        ->where(function($query) use ($dist) {
                            $query->where('name', $dist['name'])
                                ->orWhere('bn_name', $dist['bn_name']);
                        })->first();
                }

                if (!$district) {
                    $district = District::create([
                        'id' => $dist['id'],
                        'division_id' => $division->id,
                        'name' => $dist['name'],
                        'bn_name' => $dist['bn_name'],
                        'lat' => $dist['lat'],
                        'long' => $dist['long'],
                    ]);
                } else {
                    if (
                        $district->division_id !== $division->id ||
                        $district->name !== $dist['name'] ||
                        $district->bn_name !== $dist['bn_name'] ||
                        $district->lat != $dist['lat'] ||
                        $district->long != $dist['long']
                    ) {
                        $district->update([
                            'division_id' => $division->id,
                            'name' => $dist['name'],
                            'bn_name' => $dist['bn_name'],
                            'lat' => $dist['lat'],
                            'long' => $dist['long'],
                        ]);
                    }
                }

                foreach ($dist['upozila'] as $upz) {
                    // First check if upazilla exists by ID
                    $upozila = Upazilla::find($upz['id']);

                    // If not found by ID, try to find by name to prevent duplicates
                    if (!$upozila) {
                        $upozila = Upazilla::where('district_id', $district->id)
                            ->where(function($query) use ($upz) {
                                $query->where('name', $upz['name'])
                                    ->orWhere('bn_name', $upz['bn_name']);
                            })->first();
                    }

                    if (!$upozila) {
                        Upazilla::create([
                            'id' => $upz['id'],
                            'district_id' => $district->id,
                            'name' => $upz['name'],
                            'bn_name' => $upz['bn_name'],
                        ]);
                    } else {
                        if (
                            $upozila->district_id !== $district->id ||
                            $upozila->name !== $upz['name'] ||
                            $upozila->bn_name !== $upz['bn_name']
                        ) {
                            $upozila->update([
                                'district_id' => $district->id,
                                'name' => $upz['name'],
                                'bn_name' => $upz['bn_name'],
                            ]);
                        }
                    }
                }
            }
        }

        $this->info('Geo data synced successfully without redundant updates!');
    }
}
