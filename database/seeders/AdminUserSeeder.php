<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * One Super Admin, so a fresh install can be signed in to.
 *
 * Nothing about a real person: the number and the password below are made up
 * and published in the README. Change the password the first time you sign in,
 * and on a live server create your own account and delete this one.
 */
class AdminUserSeeder extends Seeder
{
    public const PHONE = '9999900000';
    public const PASSWORD = 'autobazaar';

    /** Matches the roles list: 1000 customer, 1001 admin, 1002 Super Admin. */
    private const SUPER_ADMIN_ROLE_ID = 1002;

    public function run(): void
    {
        $existing = User::where('phone_number', self::PHONE)->first();

        if ($existing) {
            $this->command?->line('  <fg=yellow>skipped</> admin user — ' . self::PHONE . ' already exists');

            return;
        }

        $user = User::create([
            'name'         => 'Super Admin',
            'email'        => 'admin@autobazaar.local',
            'phone_number' => self::PHONE,
            'password'     => Hash::make(self::PASSWORD),
            'role_id'      => self::SUPER_ADMIN_ROLE_ID,
            'status'       => 1,
        ]);

        // Spatie's role, which is what the admin panel actually checks.
        $user->assignRole('Super Admin');

        $this->command?->line('  <fg=green>seeded</>  admin user');
        $this->command?->newLine();
        $this->command?->line('  <fg=cyan>Sign in at /dashboard with</>');
        $this->command?->line('    mobile   : ' . self::PHONE);
        $this->command?->line('    password : ' . self::PASSWORD);
        $this->command?->line('  <fg=yellow>Change that password before anyone else can reach this site.</>');
        $this->command?->newLine();
    }
}
