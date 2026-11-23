
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emplacements', function (Blueprint $table) {
            $table->string('site')->nullable();
            $table->boolean('est_actif')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('emplacements', function (Blueprint $table) {
            $table->dropColumn(['site', 'est_actif']);
        });
    }
};
