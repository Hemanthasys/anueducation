<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['assigned_director_id']);
            $table->dropColumn(['school_id', 'assigned_director_id']);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_director_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};
