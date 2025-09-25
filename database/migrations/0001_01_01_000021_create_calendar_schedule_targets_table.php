<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_schedule_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_schedule_id')->constrained('calendar_schedules')->cascadeOnDelete();
            $table->enum('target_type', ['global', 'role', 'program', 'classroom', 'course']);
            $table->unsignedBigInteger('target_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_schedule_targets');
    }
};
