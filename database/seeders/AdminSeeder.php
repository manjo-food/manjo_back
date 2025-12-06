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
            'phone' => '09184185136',
        ]);

        $adminRole = Role::query()->where('name', 'admin')->first();

        $admin->roles()->syncWithoutDetaching([$adminRole->id]);
    }
}
