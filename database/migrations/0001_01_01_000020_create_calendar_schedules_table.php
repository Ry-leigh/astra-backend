<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable(); // defaults to start_date if same-day
            $table->boolean('all_day')->default(false);
            $table->time('start_time')->nullable(); // null if all-day
            $table->time('end_time')->nullable(); // null if all-day
            $table->enum('category', ['holiday', 'event', 'meeting', 'exam', 'makeup_class']);
            $table->enum('repeats', ['none', 'daily', 'weekly', 'monthly', 'yearly']);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_schedules');
    }
};
