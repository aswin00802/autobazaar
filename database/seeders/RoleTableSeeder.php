<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('roles')->delete();

        \DB::table('roles')->insert(array (
            0 =>
            array (
                'id' => '1',
                'name' => 'Super Admin',
                'guard_name' => 'web',
                'created_at' => '2025-08-08 05:32:38',
                'updated_at' => '2025-08-08 05:32:38',
            )
        ));
    }
}
