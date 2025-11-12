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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->uuid('room_id');
            $table->foreignId('participant_id')->constrained('room_participants')->onDelete('cascade');
            $table->foreignId('story_id')->constrained('stories')->onDelete('cascade');
            $table->string('vote'); // "1", "2", "3", "5", "8", "13", "?", "☕"
            $table->timestamps();

            // Ein User kann pro Story nur einmal voten
            $table->unique(['story_id', 'participant_id']);

            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->onDelete('cascade');

            $table->index(['story_id', 'participant_id']);
            $table->index('room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
