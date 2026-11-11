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
        Schema::create('equipements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->restrictOnDelete();
            $table->string('code_inventaire', 50)->unique();
            $table->string('numero_serie', 100)->nullable();
            $table->string('etat_physique', 30)->default('bon_etat'); // bon_etat, a_verifier, en_panne, hors_service
            $table->string('statut_utilisation', 30)->default('disponible'); // disponible, en_utilisation, retire_du_service
            $table->string('statut', 20)->default('actif'); // actif, archive
            $table->foreignId('emplacement_id')->nullable()->constrained('emplacements')->nullOnDelete();
            $table->date('date_entree')->nullable();
            $table->text('observations')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['produit_id', 'statut']);
            $table->index(['statut_utilisation', 'etat_physique']);
            $table->index('code_inventaire');
            $table->index('numero_serie');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipements');
    }
};
