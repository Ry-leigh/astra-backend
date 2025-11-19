<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->unsignedTinyInteger('semester');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->unique(['academic_year_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
