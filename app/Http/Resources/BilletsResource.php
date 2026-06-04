<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BilletsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        /** @var \App\Models\Billet $billet */
        $billet = $this->resource;

        return [
            'Date' => $billet->BIL_DATE,
            'Titre' => $billet->BIL_TITRE,
            'Contenu' => $billet->BIL_CONTENU,
            // Catégories du billet : permet au front de classer/filtrer les billets.
            'Categories' => CategorieResource::collection($billet->categories),
        ];
    }
}
