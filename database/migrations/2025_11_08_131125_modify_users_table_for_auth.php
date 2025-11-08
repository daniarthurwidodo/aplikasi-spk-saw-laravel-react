<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role column with enum values
            $table->enum('role', ['super_admin', 'admin', 'kepala_sekolah', 'user'])
                  ->default('user')
                  ->after('email');
            
            // Add job title for position description
            $table->string('job_title')->nullable()->after('role');
            
            // Add school foreign key
            $table->foreignId('school_id')->nullable()->constrained('schools')->after('job_title');
            
            // Add active status
            $table->boolean('is_active')->default(true)->after('school_id');
            
            // Add indexes for performance
            $table->index(['role', 'is_active']);
            $table->index('school_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropIndex(['role', 'is_active']);
            $table->dropIndex(['school_id']);
            $table->dropColumn(['role', 'job_title', 'school_id', 'is_active']);
        });
    }
};
