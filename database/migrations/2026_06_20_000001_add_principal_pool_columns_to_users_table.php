<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('service_grade')->nullable()->after('school_id');
            $table->foreignId('previous_school_id')->nullable()->after('service_grade')
                  ->constrained('schools')->nullOnDelete();
            $table->timestamp('pool_entered_at')->nullable()->after('previous_school_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['previous_school_id']);
            $table->dropColumn(['service_grade', 'previous_school_id', 'pool_entered_at']);
        });
    }
};
