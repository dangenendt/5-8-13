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
        Schema::create('jira_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('jira_domain');
            $table->string('jira_email');
            $table->text('jira_api_token'); // Will be encrypted
            $table->string('jira_project_key')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure one active configuration per user
            $table->unique(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jira_settings');
    }
};
