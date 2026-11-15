<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stocks')) {
            try {
                DB::statement('ALTER TABLE stocks DROP INDEX stocks_produit_id_emplacement_id_unique');
            } catch (\Throwable $e) {
                // Ignore if index doesn't exist
            }

            try {
                DB::statement('ALTER TABLE stocks ADD UNIQUE KEY stocks_produit_emplacement_situation_unique (produit_id, emplacement_id, situation)');
            } catch (\Throwable $e) {
                // Ignore if index already exists
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stocks')) {
            try {
                DB::statement('ALTER TABLE stocks DROP INDEX stocks_produit_emplacement_situation_unique');
            } catch (\Throwable $e) {
                // Ignore
            }

            try {
                DB::statement('ALTER TABLE stocks ADD UNIQUE KEY stocks_produit_id_emplacement_id_unique (produit_id, emplacement_id)');
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }
};
