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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name', 150)->nullable();
            $table->string('action_type', 50); // creation, modification, suppression, activation, etc.
            $table->string('module', 50); // utilisateurs, catalogue, equipements, stock, mouvements, demandes, parametres
            $table->string('entity_type', 100)->nullable(); // Produit, Equipement, Stock, User, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_name', 255)->nullable();
            $table->string('description', 255);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Index
            $table->index('user_id');
            $table->index('module');
            $table->index('action_type');
            $table->index('created_at');
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
