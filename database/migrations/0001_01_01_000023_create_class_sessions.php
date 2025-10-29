<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_schedule_id')->constrained('class_schedules')->cascadeOnDelete();
            $table->foreignId('substitute_id')->nullable()->constrained('users')->nullOnDelete(); // optional substitute instructor
            $table->date('session_date'); // actual date of the class
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->string('remarks')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('integrity_flag')->default(false);
            $table->timestamps();
            $table->unique(['class_schedule_id', 'session_date']); // prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
