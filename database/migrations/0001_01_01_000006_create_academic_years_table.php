<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->year('year_start');
            $table->year('year_end');
            $table->timestamps();
            $table->unique(['year_start', 'year_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
