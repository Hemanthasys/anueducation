<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('academic_year', 10); // e.g. "2024"

            // Student counts per grade — boys and girls
            foreach (range(1, 13) as $grade) {
                $table->unsignedSmallInteger("grade_{$grade}_boys")->default(0);
                $table->unsignedSmallInteger("grade_{$grade}_girls")->default(0);
            }

            // Disabled students
            $table->unsignedSmallInteger('disabled_boys')->default(0);
            $table->unsignedSmallInteger('disabled_girls')->default(0);

            // Meta
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Only one record per school per academic year
            $table->unique(['school_id', 'academic_year']);
            $table->index('school_id');
            $table->index('academic_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_stats');
    }
};
