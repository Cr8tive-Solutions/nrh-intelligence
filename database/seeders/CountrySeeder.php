<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Malaysia',   'code' => 'MY', 'flag' => '🇲🇾', 'region' => 'Southeast Asia'],
            ['name' => 'Singapore',  'code' => 'SG', 'flag' => '🇸🇬', 'region' => 'Southeast Asia'],
            ['name' => 'Indonesia',  'code' => 'ID', 'flag' => '🇮🇩', 'region' => 'Southeast Asia'],
            ['name' => 'Thailand',   'code' => 'TH', 'flag' => '🇹🇭', 'region' => 'Southeast Asia'],
            ['name' => 'Philippines', 'code' => 'PH', 'flag' => '🇵🇭', 'region' => 'Southeast Asia'],
            ['name' => 'Vietnam',    'code' => 'VN', 'flag' => '🇻🇳', 'region' => 'Southeast Asia'],
        ];

        foreach ($countries as $data) {
            Country::firstOrCreate(['code' => $data['code']], $data);
        }
    }
}
