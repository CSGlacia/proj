<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsDemoSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'edit properties']);
        Permission::create(['name' => 'edit listings']);
        Permission::create(['name' => 'edit reviews']);

        Permission::create(['name' => 'delete properties']);
        Permission::create(['name' => 'delete listings']);
        Permission::create(['name' => 'delete reviews']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'admin']);
        $role1->givePermissionTo('edit properties');
        $role1->givePermissionTo('edit listings');
        $role1->givePermissionTo('edit reviews');

        $role1->givePermissionTo('delete properties');
        $role1->givePermissionTo('delete listings');
        $role1->givePermissionTo('delete reviews');

        $role2 = Role::create(['name' => 'user']);

        $role3 = Role::create(['name' => 'super-admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = Factory(App\User::class)->create([
            'name' => 'Example User',
            'email' => 'test@example.com',
        ]);
        $user->assignRole($role1);

        $user = Factory(App\User::class)->create([
            'name' => 'Example Admin User',
            'email' => 'admin@example.com',
        ]);
        $user->assignRole($role2);

        $user = Factory(App\User::class)->create([
            'name' => 'Example Super-Admin User',
            'email' => 'superadmin@example.com',
        ]);
        $user->assignRole($role3);
    }
}
