<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentaireRequest;
use App\Http\Requests\UpdateCommentaireRequest;
use App\Models\Commentaire;
use Illuminate\Support\Facades\Log;

class CommentaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): void
    {
        //
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
    public function store(StoreCommentaireRequest $request): \Illuminate\Http\JsonResponse
    {
        //
        try {
            $commentaire = Commentaire::create($request->validated());
            return response()->json($commentaire,201);
        }
        catch(\Illuminate\Database\QueryException $e){
            Log::channel('projectError')->error('Erreur accès base de données');
            return response()->json([
                'message' => 'Ressource indiponible.'
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Commentaire $commentaire): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commentaire $commentaire): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentaireRequest $request, Commentaire $commentaire): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commentaire $commentaire): void
    {
        //
    }
}
