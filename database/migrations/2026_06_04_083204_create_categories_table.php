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
        // Table des catégories de billets (ex : monopalme, bi-palmes, compétition...).
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('CAT_LIBELLE'); // Libellé de la catégorie.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
