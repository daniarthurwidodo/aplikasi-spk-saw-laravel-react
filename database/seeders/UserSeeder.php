<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin (system-wide administrator)
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@spksaw.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'job_title' => 'System Administrator',
            'school_id' => null, // Super admin doesn't belong to specific school
            'is_active' => true,
        ]);

        // Get schools for assigning users
        $schools = School::all();

        if ($schools->count() > 0) {
            // Create Kepala Sekolah for each school
            foreach ($schools as $index => $school) {
                $kepalaSekolah = User::create([
                    'name' => 'Kepala Sekolah ' . $school->name,
                    'email' => 'kepala.sekolah' . ($index + 1) . '@spksaw.com',
                    'password' => Hash::make('password123'),
                    'role' => 'kepala_sekolah',
                    'job_title' => 'Kepala Sekolah',
                    'school_id' => $school->id,
                    'is_active' => true,
                ]);

                // Update school with kepala sekolah
                $school->update(['kepala_sekolah_id' => $kepalaSekolah->id]);

                // Create Admin for each school
                User::create([
                    'name' => 'Admin ' . $school->name,
                    'email' => 'admin' . ($index + 1) . '@spksaw.com',
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                    'job_title' => 'Administrator Sekolah',
                    'school_id' => $school->id,
                    'is_active' => true,
                ]);

                // Create some regular users for each school
                $userRoles = [
                    ['job_title' => 'Wakil Kepala Kurikulum', 'email_suffix' => 'waka.kurikulum'],
                    ['job_title' => 'Bendahara BOS', 'email_suffix' => 'bendahara.bos'],
                    ['job_title' => 'Staff TU', 'email_suffix' => 'staff.tu'],
                ];

                foreach ($userRoles as $userRole) {
                    User::create([
                        'name' => $userRole['job_title'] . ' ' . $school->name,
                        'email' => $userRole['email_suffix'] . ($index + 1) . '@spksaw.com',
                        'password' => Hash::make('password123'),
                        'role' => 'user',
                        'job_title' => $userRole['job_title'],
                        'school_id' => $school->id,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // Create a test user for demonstration
        User::create([
            'name' => 'Test User',
            'email' => 'test@spksaw.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'job_title' => 'Test Account',
            'school_id' => $schools->first()?->id,
            'is_active' => true,
        ]);
    }
}
