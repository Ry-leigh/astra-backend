<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->string('year_level');
            $table->string('section')->nullable()->default(NULL);
            $table->string('academic_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
