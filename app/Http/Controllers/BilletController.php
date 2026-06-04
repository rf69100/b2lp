<?php

namespace App\Http\Controllers;

use App\Http\Resources\BilletResource;
use App\Http\Resources\BilletsResource;
use App\Models\Billet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
     * Store a newly created resource in storage.
     */
    public function store(Request $request): void
    {
        //
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Billet $billet): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Billet $billet): void
    {
        //
    }
}
