<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function runOLD()
    {
        // Create Roles
        // $superAdminRole = Role::create(['name' => 'super_admin']);
        $adminRole = Role::create(['name' => 'admin']);

        // Permissions for Events
        $eventPermissions = [
            'event.create',
            'event.edit',
            'event.delete',
            'event.view',
        ];

        // Permissions for Notices
        $noticePermissions = [
            'notice.create',
            'notice.edit',
            'notice.delete',
            'notice.view',
        ];

        // Create Permissions
        foreach (array_merge($eventPermissions, $noticePermissions) as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign all permissions to super_admin
        // $superAdminRole->givePermissionTo(Permission::all());

        // Assign specific permissions to admin
        $adminRole->givePermissionTo(array_merge($eventPermissions, $noticePermissions));
    }
    public function run()
    {
        // Create Roles
        $superAdminRole = Role::create(['name' => 'super_admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Permissions for Events
        $eventPermissions = [
            'event.create',
            'event.edit',
            'event.delete',
            'event.view',
        ];

        // Permissions for Notices
        $noticePermissions = [
            'notice.create',
            'notice.edit',
            'notice.delete',
            'notice.view',
        ];

        // Array to hold permission IDs
        $permissionIds = [];

        // Create or find Permissions
        foreach (array_merge($eventPermissions, $noticePermissions) as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $permissionIds[] = $permission->id; // Collect permission IDs
        }

        // Assign permissions to admin
        $adminRole->syncPermissions($permissionIds);
    }

}
