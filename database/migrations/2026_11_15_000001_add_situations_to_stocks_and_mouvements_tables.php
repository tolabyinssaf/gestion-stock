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
        if (Schema::hasTable('stocks') && !Schema::hasColumn('stocks', 'situation')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->string('situation', 30)->default('en_stock')->after('emplacement_id')->index();
            });
        }

        if (Schema::hasTable('mouvements')) {
            if (!Schema::hasColumn('mouvements', 'situation_depart')) {
                Schema::table('mouvements', function (Blueprint $table) {
                    $table->string('situation_depart', 30)->nullable()->after('type')->index();
                });
            }
            if (!Schema::hasColumn('mouvements', 'situation_arrivee')) {
                Schema::table('mouvements', function (Blueprint $table) {
                    $table->string('situation_arrivee', 30)->nullable()->after('situation_depart')->index();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('stocks') && Schema::hasColumn('stocks', 'situation')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropColumn('situation');
            });
        }

        if (Schema::hasTable('mouvements')) {
            Schema::table('mouvements', function (Blueprint $table) {
                if (Schema::hasColumn('mouvements', 'situation_arrivee')) {
                    $table->dropColumn('situation_arrivee');
                }
                if (Schema::hasColumn('mouvements', 'situation_depart')) {
                    $table->dropColumn('situation_depart');
                }
            });
        }
    }
};
