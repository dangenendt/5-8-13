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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->uuid('room_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('3rd_party_ident')->nullable(); // z.B. "PROJ-123"
            $table->string('3rd_party_url')->nullable();
            $table->string('final_estimate')->nullable(); // Consensus-Ergebnis
            $table->integer('sort_order')->default(0); // Für Reihenfolge
            $table->timestamp('voting_started_at')->nullable();
            $table->timestamp('revealed_at')->nullable();
            $table->timestamps();

            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->onDelete('cascade');

            $table->index(['room_id', 'sort_order']);
            $table->index('3rd_party_ident');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
