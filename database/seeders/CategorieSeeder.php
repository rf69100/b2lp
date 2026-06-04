<?php

namespace Database\Seeders;

use App\Models\Billet;
use App\Models\Categorie;
use Illuminate\Database\Seeder;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * À lancer APRÈS le BilletSeeder pour pouvoir rattacher des catégories aux billets.
     */
    public function run(): void
    {
        $libelles = [
            'monopalme',
            'bi-palmes',
            'compétition',
            'sécurité',
            'randonnée palmée',
        ];

        $categories = collect($libelles)->map(function (string $libelle) {
            return Categorie::firstOrCreate(['CAT_LIBELLE' => $libelle]);
        });

        // On rattache 1 à 3 catégories aléatoires à chaque billet existant.
        Billet::all()->each(function (Billet $billet) use ($categories) {
            $billet->categories()->syncWithoutDetaching(
                $categories->random(rand(1, 3))->pluck('id')->all()
            );
        });
    }
}
