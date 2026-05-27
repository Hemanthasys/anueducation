<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('module', [
                'school_info',
                'student_stats',
                'physical_resources',
                'resource_programs',
                'quality_circle',
                'staff',
                'news',
                'notice',
                'download',
                'user_management',
                'other',
            ]);
            $table->enum('action', [
                'created',
                'updated',
                'deleted',
                'submitted',
                'approved',
                'rejected',
                'uploaded',
                'downloaded',
            ]);
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes for filtering
            $table->index('user_id');
            $table->index('module');
            $table->index('action');
            $table->index('school_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
