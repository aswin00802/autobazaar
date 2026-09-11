<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Hash;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('users')->where('role_id',1002)->delete();
        $user = User::create([
            'role_id'   => 1002,
            'name'      => 'Super Admin',
            // 'username'  => 'superadmin',
            'email'     => 'muruganrk25239@gmail.com',
            'phone_number'    => '9962003696',
            'password'  => Hash::make('123456'),
            'status' => 1,
        ]);

        $role = Role::find(1002);

        // $permissions = Permission::pluck('id', 'id')->all();

        // $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);

        $admin_role = Role::find(1001);
        $admin_user = User::where('email','admin@gmail.com')->first();
        $admin_user->assignRole([$admin_role->id]);

    }
}
