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
        Schema::table('schools', function (Blueprint $table) {
            // Add kepala sekolah foreign key
            $table->foreignId('kepala_sekolah_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('district');
            
            // Add index for performance
            $table->index('kepala_sekolah_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign(['kepala_sekolah_id']);
            $table->dropIndex(['kepala_sekolah_id']);
            $table->dropColumn('kepala_sekolah_id');
        });
    }
};
