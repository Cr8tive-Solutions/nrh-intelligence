<?php

namespace Database\Seeders;

use App\Models\IdentityType;
use Illuminate\Database\Seeder;

class IdentityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['NRIC', 'Passport', 'Army / Police ID', 'MyPR'];

        foreach ($types as $name) {
            IdentityType::firstOrCreate(['name' => $name]);
        }
    }
}
