<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBilletRequest extends FormRequest
{
    /**
     * Autorisation : seul l'administrateur peut modifier un billet (délégué à BilletPolicy::update).
     * Le billet ciblé est résolu par le model binding de la route (paramètre {billet}).
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('billet')) ?? false;
    }

    /**
     * Règles de validation pour la modification d'un billet.
     * Tous les champs sont optionnels (modification partielle) grâce à 'sometimes'.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'BIL_TITRE' => ['sometimes', 'required', 'string', 'max:255'],
            'BIL_CONTENU' => ['sometimes', 'required', 'string'],
            // Si fourni, remplace l'ensemble des catégories du billet.
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
