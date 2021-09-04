<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $actions = ['index', 'store', 'show', 'update', 'destroy'];

        foreach ($actions as $key) {
            Permission::updateOrCreate(['name' => 'user.' . $key]);
        }

        $user = User::updateOrCreate(
            [
                'email' => 'admin@admin.com',
            ],
            [
                'name' => 'Admin',
                'password' => bcrypt('123456'),
            ]
        );

        $user->syncRoles(Role::ADMIN);
    }
}
