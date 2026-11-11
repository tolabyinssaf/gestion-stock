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
        Schema::create('mouvements', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 50)->unique();
            $table->string('type', 30); // entree, sortie, transfert, correction
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->foreignId('equipement_id')->nullable()->constrained('equipements')->nullOnDelete();
            $table->unsignedInteger('quantite')->default(1);
            $table->foreignId('emplacement_source_id')->nullable()->constrained('emplacements')->nullOnDelete();
            $table->foreignId('emplacement_destination_id')->nullable()->constrained('emplacements')->nullOnDelete();
            $table->string('motif', 255);
            $table->string('beneficiaire', 150)->nullable();
            $table->text('commentaire')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date_mouvement');
            $table->timestamps();

            $table->index(['type', 'date_mouvement']);
            $table->index(['produit_id', 'type']);
            $table->index(['equipement_id', 'type']);
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements');
    }
};
