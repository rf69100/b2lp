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
        // Table pivot de la relation plusieurs-à-plusieurs entre billets et catégories.
        // Un billet peut avoir plusieurs catégories, une catégorie peut concerner plusieurs billets.
        Schema::create('billet_categorie', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billet_id');
            $table->unsignedBigInteger('categorie_id');
            // Clés étrangères : si un billet ou une catégorie est supprimé, les liens associés le sont aussi (cascade).
            $table->foreign('billet_id')
                ->references('id')
                ->on('billets')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('categorie_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            // Empêche d'associer deux fois la même catégorie à un même billet.
            $table->unique(['billet_id', 'categorie_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billet_categorie');
    }
};
