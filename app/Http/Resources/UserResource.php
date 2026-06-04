<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        /** @var \App\Models\User $user */
        $user = $this->resource;

        return [
            'id' => $user->getKey(),
            'nom' => $user->name,
            'email' => $user->email,
            // Rôle exposé pour que le front puisse afficher/masquer les actions CRUD réservées à l'admin.
            'role' => $user->role,
        ];
    }
}
