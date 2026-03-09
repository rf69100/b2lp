<?php

namespace App\Http\Controllers;

use App\Http\Resources\{BilletsResource,BilletResource};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Models\Billet;

class BilletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        try {
            //Le résultat de la requête est retourné directement en JSON
            //return Billet::all();
            return response()->json(BilletsResource::collection(Billet::all()));
        }
        catch(\Illuminate\Database\QueryException $e) {
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
            $billetResource = new BilletResource(Billet::with('commentaires','commentaires.user')->findOrFail($id));
            return response()->json($billetResource);
        }
        catch(\Illuminate\Database\QueryException $e) {
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
