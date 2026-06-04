<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategorieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Categorie $categorie */
        $categorie = $this->resource;

        return [
            'Id' => $categorie->id,
            'Libelle' => $categorie->CAT_LIBELLE,
        ];
    }
}
