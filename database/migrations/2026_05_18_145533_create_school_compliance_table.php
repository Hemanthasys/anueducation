<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('school_compliance', function (Blueprint $table) {
        $table->id();
        $table->foreignId('school_id')->constrained()->cascadeOnDelete();
        $table->foreignId('stat_deadline_id')->constrained()->cascadeOnDelete();
        $table->enum('status', ['pending', 'submitted', 'overdue'])->default('pending');
        $table->timestamp('submitted_at')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('school_compliance');
}
};
