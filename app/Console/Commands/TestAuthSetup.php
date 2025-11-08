<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestAuthSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test authentication setup and database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Authentication Setup...');
        
        // Test database connection
        try {
            $schoolCount = School::count();
            $userCount = User::count();
            
            $this->line("📊 Database Status:");
            $this->line("   Schools: {$schoolCount}");
            $this->line("   Users: {$userCount}");
            
            if ($schoolCount === 0 || $userCount === 0) {
                $this->error("❌ Database is empty! Run: php artisan db:seed");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Database connection failed: " . $e->getMessage());
            return 1;
        }
        
        // Test users with different roles
        $this->line("\n👥 User Roles:");
        
        $roles = ['super_admin', 'admin', 'kepala_sekolah', 'user'];
        foreach ($roles as $role) {
            $count = User::where('role', $role)->count();
            $this->line("   {$role}: {$count} users");
        }
        
        // Test super admin
        $superAdmin = User::where('role', 'super_admin')->first();
        if ($superAdmin) {
            $this->line("\n🔐 Super Admin Account:");
            $this->line("   Email: {$superAdmin->email}");
            $this->line("   Name: {$superAdmin->name}");
            $this->line("   Active: " . ($superAdmin->is_active ? 'Yes' : 'No'));
            
            // Test password
            if (Hash::check('password123', $superAdmin->password)) {
                $this->info("   ✅ Password verification successful");
            } else {
                $this->error("   ❌ Password verification failed");
            }
        }
        
        // Test school relationships
        $this->line("\n🏫 School Relationships:");
        $schoolsWithKepala = School::whereNotNull('kepala_sekolah_id')->count();
        $this->line("   Schools with Kepala Sekolah: {$schoolsWithKepala}");
        
        // Test JWT configuration
        $this->line("\n🔑 JWT Configuration:");
        if (config('jwt.secret')) {
            $this->info("   ✅ JWT secret is configured");
        } else {
            $this->error("   ❌ JWT secret is missing");
        }
        
        $this->line("   TTL: " . config('jwt.ttl') . " minutes");
        
        // Test sample login credentials
        $this->line("\n📋 Sample Login Credentials:");
        $this->line("   Super Admin:");
        $this->line("     Email: superadmin@spksaw.com");
        $this->line("     Password: password123");
        
        $adminUser = User::where('role', 'admin')->first();
        if ($adminUser) {
            $this->line("   Admin:");
            $this->line("     Email: {$adminUser->email}");
            $this->line("     Password: password123");
        }
        
        $this->line("\n🌐 API Endpoints:");
        $this->line("   POST /api/auth/login");
        $this->line("   POST /api/auth/logout");
        $this->line("   POST /api/auth/refresh");
        $this->line("   GET  /api/auth/me");
        
        $this->info("\n✅ Authentication setup test completed successfully!");
        return 0;
    }
}
