<?php

namespace Tests\Feature;

use App\Models\Billet;
use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BilletCategorieTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_billet_peut_avoir_plusieurs_categories(): void
    {
        $billet = Billet::factory()->create();
        $mono = Categorie::create(['CAT_LIBELLE' => 'monopalme']);
        $secu = Categorie::create(['CAT_LIBELLE' => 'sécurité']);

        $billet->categories()->attach([$mono->id, $secu->id]);

        $this->assertCount(2, $billet->refresh()->categories);
        $this->assertTrue($billet->categories->contains('CAT_LIBELLE', 'monopalme'));
    }

    public function test_une_categorie_peut_avoir_plusieurs_billets(): void
    {
        $categorie = Categorie::create(['CAT_LIBELLE' => 'compétition']);
        $billets = Billet::factory(3)->create();

        $categorie->billets()->attach($billets->pluck('id'));

        $this->assertCount(3, $categorie->refresh()->billets);
    }

    public function test_accesseur_libelles_categories_retourne_les_libelles(): void
    {
        $billet = Billet::factory()->create();
        $billet->categories()->attach([
            Categorie::create(['CAT_LIBELLE' => 'monopalme'])->id,
            Categorie::create(['CAT_LIBELLE' => 'randonnée palmée'])->id,
        ]);

        $libelles = $billet->refresh()->libelles_categories;

        $this->assertEqualsCanonicalizing(
            ['monopalme', 'randonnée palmée'],
            $libelles
        );
    }

    public function test_la_liste_des_billets_expose_les_categories(): void
    {
        $billet = Billet::factory()->create();
        $billet->categories()->attach(
            Categorie::create(['CAT_LIBELLE' => 'bi-palmes'])->id
        );

        $response = $this->getJson('/api/billets');

        $response->assertOk()
            ->assertJsonFragment(['Libelle' => 'bi-palmes'])
            ->assertJsonStructure([['Date', 'Titre', 'Contenu', 'Categories' => [['Id', 'Libelle']]]]);
    }

    public function test_le_detail_dun_billet_expose_les_categories(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $billet = Billet::factory()->create();
        $billet->categories()->attach(
            Categorie::create(['CAT_LIBELLE' => 'sécurité'])->id
        );

        $response = $this->getJson("/api/billets/{$billet->id}");

        $response->assertOk()
            ->assertJsonFragment(['Libelle' => 'sécurité'])
            ->assertJsonStructure(['Date', 'Titre', 'Contenu', 'Categories', 'Commentaires']);
    }

    public function test_on_peut_filtrer_les_billets_par_categorie(): void
    {
        $compet = Categorie::create(['CAT_LIBELLE' => 'compétition']);
        $rando = Categorie::create(['CAT_LIBELLE' => 'randonnée palmée']);

        $billetCompet = Billet::factory()->create(['BIL_TITRE' => 'Course du dimanche']);
        $billetRando = Billet::factory()->create(['BIL_TITRE' => 'Balade tranquille']);

        $billetCompet->categories()->attach($compet->id);
        $billetRando->categories()->attach($rando->id);

        $response = $this->getJson('/api/billets?categorie_id='.$compet->id);

        $response->assertOk()
            ->assertJsonFragment(['Titre' => 'Course du dimanche'])
            ->assertJsonMissing(['Titre' => 'Balade tranquille']);
    }
}
