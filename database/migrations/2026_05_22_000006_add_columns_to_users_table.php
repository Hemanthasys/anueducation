<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            // $table->string('nic')->nullable()->after('username');
            $table->date('birthday')->nullable()->after('nic');
            $table->date('appointed_date')->nullable()->after('birthday');
            // $table->string('photo')->nullable()->after('appointed_date');
            $table->foreignId('subject_id')->nullable()->after('photo')->constrained('subjects')->nullOnDelete();
            $table->foreignId('division_id')->nullable()->after('subject_id')->constrained('divisions')->nullOnDelete();
            $table->boolean('must_change_password')->default(true)->after('division_id');
            // $table->boolean('is_active')->default(true)->after('must_change_password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['division_id']);
            $table->dropColumn([
                'username', 'birthday', 'appointed_date', 'subject_id', 'division_id',
                'must_change_password',
            ]);
        });
    }
};
