<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentaireResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        /** @var \App\Models\Commentaire $commentaire */
        $commentaire = $this->resource;
        return [
            'Date' => $commentaire->COM_DATE,
            'Auteur' => $commentaire->user->name,
            'Contenu' => $commentaire->COM_CONTENU,
        ];
    }
}
