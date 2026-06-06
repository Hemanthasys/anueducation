<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE contact_messages MODIFY COLUMN status ENUM('new','read','assigned','replied') NOT NULL DEFAULT 'new'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE contact_messages MODIFY COLUMN status ENUM('new','assigned','replied') NOT NULL DEFAULT 'new'");
    }
};
