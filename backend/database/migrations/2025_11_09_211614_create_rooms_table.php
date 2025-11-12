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
        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique(); // Für schöne URLs: /room/my-sprint-planning
            $table->string('card_deck')->default('fibonacci'); // fibonacci, tshirt, modified_fibonacci, powers_of_2
            $table->boolean('allow_observers')->default(true);
            $table->integer('voting_time_limit')->nullable(); // Sekunden, null = unbegrenzt
            $table->string('admin_token')->unique()->nullable(); // Token für Admin-Zugang
            $table->timestamps();
            $table->softDeletes();
            $table->index('slug');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
