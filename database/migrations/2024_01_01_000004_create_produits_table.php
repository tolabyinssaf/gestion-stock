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
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categories')->restrictOnDelete();
            $table->string('nom', 150);
            $table->string('marque', 80);
            $table->string('modele', 100)->nullable();
            $table->string('reference', 50)->unique();
            $table->string('type_suivi', 20)->default('individuel'); // individuel, quantite
            $table->text('description')->nullable();
            $table->text('caracteristiques')->nullable();
            $table->string('photo', 255)->nullable();
            $table->decimal('prix_indicatif', 10, 2)->nullable();
            $table->unsignedInteger('seuil_minimum')->nullable()->default(0);
            $table->string('statut', 20)->default('actif'); // actif, archive
            $table->timestamps();

            $table->index(['categorie_id', 'statut']);
            $table->index(['type_suivi', 'statut']);
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
