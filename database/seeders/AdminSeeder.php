<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->firstOrCreate([
            'username' => 'Admin',
        ], [
            'password' => Hash::make('31<|11$$nhBl'),
            'phone' => '09999999999',
        ]);

        $adminRole = Role::query()->where('name', 'super_admin')->first();

        $admin->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
