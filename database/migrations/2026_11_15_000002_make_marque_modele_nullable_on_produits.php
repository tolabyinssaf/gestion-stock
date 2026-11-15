<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->string('marque', 80)->nullable()->default(null)->change();
            $table->string('modele', 100)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->string('marque', 80)->nullable(false)->default('')->change();
            $table->string('modele', 100)->nullable(false)->default('')->change();
        });
    }
};
