<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('nic')->nullable()->unique()->after('phone');
            $table->string('designation')->nullable()->after('nic');
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete()->after('designation');
            $table->boolean('is_active')->default(true)->after('school_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn(['phone', 'nic', 'designation', 'school_id', 'is_active']);
        });
    }
};