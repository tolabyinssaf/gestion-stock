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
        if (Schema::hasTable('demandes_materiel') && !Schema::hasColumn('demandes_materiel', 'prix_estime')) {
            Schema::table('demandes_materiel', function (Blueprint $table) {
                $table->decimal('prix_estime', 10, 2)->nullable()->after('priorite');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('demandes_materiel') && Schema::hasColumn('demandes_materiel', 'prix_estime')) {
            Schema::table('demandes_materiel', function (Blueprint $table) {
                $table->dropColumn('prix_estime');
            });
        }
    }
};
