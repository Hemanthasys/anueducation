<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('address_si')->nullable()->after('address');
            $table->enum('ownership', ['national', 'provincial'])->default('provincial')->after('medium');
            $table->enum('convenience_level', ['easy', 'difficult', 'very_difficult', 'more_convenient'])->nullable()->after('ownership');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['address_si', 'ownership', 'convenience_level']);
        });
    }
};