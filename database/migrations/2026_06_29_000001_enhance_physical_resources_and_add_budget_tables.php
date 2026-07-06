<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Step 1: Enhance school_physical_resources ─────────────────
        Schema::table('school_physical_resources', function (Blueprint $table) {

            // Classrooms breakdown (after classrooms_count)
            $table->unsignedInteger('classrooms_usable')->default(0)->after('classrooms_count');
            $table->unsignedInteger('classrooms_unusable')->default(0)->after('classrooms_usable');
            $table->unsignedInteger('classrooms_to_repair')->default(0)->after('classrooms_unusable');
            $table->unsignedInteger('classrooms_to_demolish')->default(0)->after('classrooms_to_repair');

            // Teachers quarters breakdown (after teachers_quarters)
            $table->unsignedInteger('teachers_quarters_count')->default(0)->after('teachers_quarters');
            $table->unsignedInteger('teachers_quarters_usable')->default(0)->after('teachers_quarters_count');
            $table->unsignedInteger('teachers_quarters_unusable')->default(0)->after('teachers_quarters_usable');
            $table->unsignedInteger('teachers_quarters_to_repair')->default(0)->after('teachers_quarters_unusable');
            $table->unsignedInteger('teachers_quarters_to_demolish')->default(0)->after('teachers_quarters_to_repair');

            // Principals quarters (new — after teachers_quarters_to_demolish)
            $table->boolean('principals_quarters')->default(false)->after('teachers_quarters_to_demolish');
            $table->unsignedInteger('principals_quarters_count')->default(0)->after('principals_quarters');
            $table->unsignedInteger('principals_quarters_usable')->default(0)->after('principals_quarters_count');
            $table->unsignedInteger('principals_quarters_unusable')->default(0)->after('principals_quarters_usable');
            $table->unsignedInteger('principals_quarters_to_repair')->default(0)->after('principals_quarters_unusable');
            $table->unsignedInteger('principals_quarters_to_demolish')->default(0)->after('principals_quarters_to_repair');

            // Hostel breakdown (after hostel)
            $table->unsignedInteger('hostel_count')->default(0)->after('hostel');
            $table->unsignedInteger('hostel_boys')->default(0)->after('hostel_count');
            $table->unsignedInteger('hostel_girls')->default(0)->after('hostel_boys');

            // Remove old finance fields
            $table->dropColumn([
                'annual_budget',
                'sbm_funds',
                'donor_contributions',
                'ngo_support',
                'infrastructure_grants',
            ]);
        });

        // ── Step 2: School budget income table ────────────────────────
        Schema::create('school_budget_income', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('academic_year', 4);
            $table->foreignId('funding_source_id')->constrained('funding_sources')->cascadeOnDelete();
            $table->decimal('expected_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['school_id', 'academic_year', 'funding_source_id'], 'budget_income_unique');
        });

        // ── Step 3: School budget expenditure table ───────────────────
        Schema::create('school_budget_expenditure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('academic_year', 4);
            $table->foreignId('expenditure_vote_id')->constrained('expenditure_votes')->cascadeOnDelete();
            $table->decimal('expected_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['school_id', 'academic_year', 'expenditure_vote_id'], 'budget_expenditure_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_budget_expenditure');
        Schema::dropIfExists('school_budget_income');

        Schema::table('school_physical_resources', function (Blueprint $table) {
            $table->dropColumn([
                'classrooms_usable', 'classrooms_unusable',
                'classrooms_to_repair', 'classrooms_to_demolish',
                'teachers_quarters_count', 'teachers_quarters_usable',
                'teachers_quarters_unusable', 'teachers_quarters_to_repair',
                'teachers_quarters_to_demolish',
                'principals_quarters', 'principals_quarters_count',
                'principals_quarters_usable', 'principals_quarters_unusable',
                'principals_quarters_to_repair', 'principals_quarters_to_demolish',
                'hostel_count', 'hostel_boys', 'hostel_girls',
            ]);
            $table->decimal('annual_budget', 15, 2)->nullable();
            $table->decimal('sbm_funds', 15, 2)->nullable();
            $table->boolean('donor_contributions')->default(false);
            $table->boolean('ngo_support')->default(false);
            $table->boolean('infrastructure_grants')->default(false);
        });
    }
};
