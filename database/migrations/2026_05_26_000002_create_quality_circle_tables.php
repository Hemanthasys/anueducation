<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Main inspection record per school per year
        Schema::create('quality_circle_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('academic_year', 9);             // e.g. 2025
            $table->date('inspection_date');
            $table->foreignId('inspected_by')->nullable()   // user from dropdown
                  ->constrained('users')->onDelete('set null');
            $table->string('inspector_name')->nullable();   // manual name if not in system
            $table->string('inspector_designation')->nullable(); // manual designation
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])
                  ->default('draft');
            $table->decimal('final_index', 5, 2)->nullable(); // පාසල් අධ්‍යාපන ගුණාත්මක දර්ශකය
            $table->foreignId('approved_by')->nullable()
                  ->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_note')->nullable();
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')->onDelete('set null');
            $table->timestamps();

            // One record per school per year
            $table->unique(['school_id', 'academic_year']);
        });

        // Marks for each of the 8 criteria per record
        Schema::create('quality_circle_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('record_id')
                  ->constrained('quality_circle_records')->onDelete('cascade');
            $table->foreignId('criteria_id')
                  ->constrained('quality_circle_criteria')->onDelete('cascade');
            $table->unsignedSmallInteger('indicators_assessed')->default(0); // ඇගයීම් කළ දර්ශක සංඛ්‍යාව
            $table->unsignedSmallInteger('maximum_marks')->default(0);       // උපරිම ලකුණ
            $table->unsignedSmallInteger('obtained_marks')->default(0);      // ලබා ගත් ලකුණු
            $table->decimal('percentage', 5, 2)->default(0);                 // ප්‍රතිශතය (calculated)
            $table->timestamps();

            $table->unique(['record_id', 'criteria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_circle_marks');
        Schema::dropIfExists('quality_circle_records');
    }
};
