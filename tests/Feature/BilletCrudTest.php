<?php

namespace Tests\Feature;

use App\Models\Billet;
use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BilletCrudTest extends TestCase
{
    use RefreshDatabase;

    // ---------- Création ----------

    public function test_un_admin_peut_creer_un_billet(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $response = $this->postJson('/api/billets', [
            'BIL_TITRE' => 'Nouveau billet',
            'BIL_CONTENU' => 'Contenu du billet.',
        ]);

        $response->assertCreated()
            ->assertJsonFragment(['Titre' => 'Nouveau billet']);

        $this->assertDatabaseHas('billets', ['BIL_TITRE' => 'Nouveau billet']);
    }

    public function test_un_admin_peut_creer_un_billet_avec_des_categories(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());
        $cat = Categorie::create(['CAT_LIBELLE' => 'monopalme']);

        $response = $this->postJson('/api/billets', [
            'BIL_TITRE' => 'Billet catégorisé',
            'BIL_CONTENU' => 'Contenu.',
            'categorie_ids' => [$cat->id],
        ]);

        $response->assertCreated()
            ->assertJsonFragment(['Libelle' => 'monopalme']);
    }

    public function test_un_client_ne_peut_pas_creer_un_billet(): void
    {
        Sanctum::actingAs(User::factory()->create()); // rôle client par défaut

        $response = $this->postJson('/api/billets', [
            'BIL_TITRE' => 'Tentative',
            'BIL_CONTENU' => 'Contenu.',
        ]);

        $response->assertForbidden(); // 403
        $this->assertDatabaseMissing('billets', ['BIL_TITRE' => 'Tentative']);
    }

    public function test_un_visiteur_non_authentifie_ne_peut_pas_creer_un_billet(): void
    {
        $response = $this->postJson('/api/billets', [
            'BIL_TITRE' => 'Anonyme',
            'BIL_CONTENU' => 'Contenu.',
        ]);

        $response->assertUnauthorized(); // 401
    }

    public function test_la_creation_valide_les_champs_requis(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $response = $this->postJson('/api/billets', []);

        $response->assertStatus(422)
            ->assertJsonFragment(['success' => false]);
    }

    // ---------- Modification ----------

    public function test_un_admin_peut_modifier_un_billet(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());
        $billet = Billet::factory()->create(['BIL_TITRE' => 'Ancien titre']);

        $response = $this->putJson("/api/billets/{$billet->id}", [
            'BIL_TITRE' => 'Titre modifié',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['Titre' => 'Titre modifié']);

        $this->assertDatabaseHas('billets', ['id' => $billet->id, 'BIL_TITRE' => 'Titre modifié']);
    }

    public function test_un_client_ne_peut_pas_modifier_un_billet(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $billet = Billet::factory()->create(['BIL_TITRE' => 'Intact']);

        $response = $this->putJson("/api/billets/{$billet->id}", [
            'BIL_TITRE' => 'Hack',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('billets', ['id' => $billet->id, 'BIL_TITRE' => 'Intact']);
    }

    // ---------- Suppression ----------

    public function test_un_admin_peut_supprimer_un_billet(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());
        $billet = Billet::factory()->create();

        $response = $this->deleteJson("/api/billets/{$billet->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('billets', ['id' => $billet->id]);
    }

    public function test_un_client_ne_peut_pas_supprimer_un_billet(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $billet = Billet::factory()->create();

        $response = $this->deleteJson("/api/billets/{$billet->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('billets', ['id' => $billet->id]);
    }

    // ---------- Helper métier ----------

    public function test_is_admin_distingue_les_roles(): void
    {
        $this->assertTrue(User::factory()->admin()->create()->isAdmin());
        $this->assertFalse(User::factory()->create()->isAdmin());
    }
}
