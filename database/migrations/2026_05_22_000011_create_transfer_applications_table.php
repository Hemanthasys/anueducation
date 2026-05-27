<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Placeholder — full procedure TBD
        Schema::create('transfer_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('to_school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'principal_review', 'officer_review', 'approved', 'rejected'])->default('draft');
            $table->text('reason')->nullable();
            $table->text('principal_comment')->nullable();
            $table->text('officer_comment')->nullable();
            $table->text('director_decision')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_applications');
    }
};
