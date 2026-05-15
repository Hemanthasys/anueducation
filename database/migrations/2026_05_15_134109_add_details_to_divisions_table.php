<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->text('address')->nullable()->after('director_id');
            $table->string('phone')->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->string('google_map_url')->nullable()->after('email');
            $table->foreignId('acting_director_id')->nullable()->constrained('users')->nullOnDelete()->after('google_map_url');
        });
    }

    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign(['acting_director_id']);
            $table->dropColumn(['address', 'phone', 'email', 'google_map_url', 'acting_director_id']);
        });
    }
};