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
        Schema::create('demandes_materiel', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 40)->unique();
            $table->foreignId('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('quantite')->default(1);
            $table->enum('priorite', ['normale', 'importante', 'urgente'])->default('normale');
            $table->string('motif', 255);
            $table->text('commentaire')->nullable();
            $table->enum('statut_preparation', ['brouillon', 'prete', 'ouverture_gmail'])->default('brouillon');

            // Snapshot des données au moment de la demande
            $table->string('produit_designation', 255)->nullable();
            $table->string('produit_reference', 100)->nullable();
            $table->string('produit_categorie', 100)->nullable();
            $table->integer('stock_disponible_snapshot')->default(0);

            // Données e-mail
            $table->string('destinataire_nom', 150)->nullable();
            $table->string('email_destinataire', 255);
            $table->string('objet_email', 255);
            $table->text('corps_email');
            $table->dateTime('date_ouverture_gmail')->nullable();

            $table->boolean('est_archive')->default(false);
            $table->timestamps();

            // Index de recherche
            $table->index('reference');
            $table->index('produit_id');
            $table->index('user_id');
            $table->index('statut_preparation');
            $table->index('priorite');
            $table->index('est_archive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demandes_materiel');
    }
};
