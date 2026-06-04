<?php

namespace App\Http\Requests;

use App\Models\Billet;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBilletRequest extends FormRequest
{
    /**
     * Autorisation : seul l'administrateur peut créer un billet (délégué à BilletPolicy::create).
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Billet::class) ?? false;
    }

    /**
     * Règles de validation pour la création d'un billet.
     * BIL_DATE n'est pas accepté du client : il est défini côté serveur dans le contrôleur.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'BIL_TITRE' => ['required', 'string', 'max:255'],
            'BIL_CONTENU' => ['required', 'string'],
            // Catégories optionnelles à rattacher au billet (tableau d'ids existants).
            'categorie_ids' => ['sometimes', 'array'],
            'categorie_ids.*' => ['integer', 'exists:categories,id'],
        ];
    }

    /**
     * Retourne toujours les erreurs de validation en JSON (cohérent avec StoreCommentaireRequest).
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors(),
        ], 422));
    }
}
