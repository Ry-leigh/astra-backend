<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->enum('target_type', ['global', 'role', 'program', 'classroom', 'course']);
            $table->unsignedBigInteger('target_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_targets');
    }
};
