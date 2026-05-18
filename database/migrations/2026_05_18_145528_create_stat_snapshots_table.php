<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('stat_snapshots', function (Blueprint $table) {
        $table->id();
        $table->string('academic_year');
        $table->integer('total_students')->default(0);
        $table->integer('total_teachers')->default(0);
        $table->integer('total_schools')->default(0);
        $table->integer('total_divisions')->default(0);
        $table->timestamp('generated_at');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('stat_snapshots');
}
};
