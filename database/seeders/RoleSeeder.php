<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin','teacher','parent'] as $role) {
            Role::findOrCreate($role, 'web');
        }
    }
}
