<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin::firstOrCreate(
            ['email' => 'admin@nrhintelligence.com'],
            [
                'name'     => 'Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('Admin@1234'),
                'role'     => 'super_admin',
                'status'   => 'active',
            ]
        );

        \App\Models\Admin::firstOrCreate(
            ['email' => 'ops@nrhintelligence.com'],
            [
                'name'     => 'Operations Team',
                'password' => \Illuminate\Support\Facades\Hash::make('Admin@1234'),
                'role'     => 'operations',
                'status'   => 'active',
            ]
        );
    }
}
