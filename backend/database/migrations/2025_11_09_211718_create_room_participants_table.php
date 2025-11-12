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
        Schema::create('room_participants', function (Blueprint $table) {
            $table->id();
            $table->uuid('room_id');
            $table->string('name');
            $table->enum('role', ['admin', 'participant', 'observer'])->default('participant');
            $table->string('session_id')->unique(); // Für Reconnect ohne Login
            $table->boolean('is_online')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->onDelete('cascade');

            $table->index(['room_id', 'is_online']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_participants');
    }
};
