<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBilletRequest;
use App\Http\Requests\UpdateBilletRequest;
use App\Http\Resources\BilletResource;
use App\Http\Resources\BilletsResource;
use App\Models\Billet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BilletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            // Les catégories sont chargées avec les billets (eager loading) pour éviter les requêtes N+1.
            $query = Billet::with('categories');

            // Filtre optionnel : permet de classer les billets par catégorie.
            // Ex : GET /api/billets?categorie_id=3
            if ($request->filled('categorie_id')) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('categories.id', $request->query('categorie_id'));
                });
            }

            return response()->json(BilletsResource::collection($query->get()));
        } catch (\Illuminate\Database\QueryException $e) {
            Log::channel('projectLog')->error('Erreur accès base de données');

            return response()->json([
                'message' => 'Ressource indisponible.'], 500);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Crée un nouveau billet.
     * Réservé à l'administrateur : l'autorisation est vérifiée par StoreBilletRequest (BilletPolicy::create).
     */
    public function store(StoreBilletRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $billet = Billet::create([
                // La date est définie côté serveur, jamais envoyée par le client (comme pour les commentaires).
                'BIL_DATE' => now()->toDateString(),
                'BIL_TITRE' => $request->validated('BIL_TITRE'),
                'BIL_CONTENU' => $request->validated('BIL_CONTENU'),
            ]);

            // Rattachement des catégories éventuellement fournies.
            if ($request->filled('categorie_ids')) {
                $billet->categories()->sync($request->validated('categorie_ids'));
            }

            return response()->json(
                new BilletResource($billet->load('categories', 'commentaires.user')),
                201
            );
        } catch (\Illuminate\Database\QueryException $e) {
            Log::channel('projectLog')->error('Erreur accès base de données');

            return response()->json([
                'message' => 'Ressource indisponible.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        //
        try {
            $billetResource = new BilletResource(Billet::with('categories', 'commentaires', 'commentaires.user')->findOrFail($id));

            return response()->json($billetResource);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Erreur accès base de données');

            return response()->json([
                'message' => 'Ressource indisponible.'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Billet $billet): void
    {
        //
    }

    /**
     * Modifie un billet existant.
     * Réservé à l'administrateur : l'autorisation est vérifiée par UpdateBilletRequest (BilletPolicy::update).
     * Le billet est résolu par le model binding de la route (paramètre {billet}).
     */
    public function update(UpdateBilletRequest $request, Billet $billet): \Illuminate\Http\JsonResponse
    {
        try {
            // Met à jour uniquement les champs validés réellement fournis.
            $billet->update($request->safe()->only(['BIL_TITRE', 'BIL_CONTENU']));

            // Si des catégories sont fournies, elles remplacent l'ensemble actuel du billet.
            if ($request->has('categorie_ids')) {
                $billet->categories()->sync($request->validated('categorie_ids'));
            }

            return response()->json(
                new BilletResource($billet->load('categories', 'commentaires.user'))
            );
        } catch (\Illuminate\Database\QueryException $e) {
            Log::channel('projectLog')->error('Erreur accès base de données');

            return response()->json([
                'message' => 'Ressource indisponible.'], 500);
        }
    }

    /**
     * Supprime un billet.
     * Réservé à l'administrateur : l'autorisation est vérifiée via BilletPolicy::delete.
     */
    public function destroy(Billet $billet): \Illuminate\Http\JsonResponse
    {
        // Lève une 403 (JSON) si l'utilisateur n'est pas autorisé à supprimer.
        $this->authorize('delete', $billet);

        try {
            $billet->delete();

            return response()->json(['message' => 'Billet supprimé.']);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::channel('projectLog')->error('Erreur accès base de données');

            return response()->json([
                'message' => 'Ressource indisponible.'], 500);
        }
    }
}
