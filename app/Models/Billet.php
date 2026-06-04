<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billet extends Model
{
    /** @use HasFactory<\Database\Factories\BilletFactory> */
    use HasFactory;

    protected $fillable = [
        'BIL_DATE',
        'BIL_TITRE',
        'BIL_CONTENU',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    // Un billet a plusieurs commentaires.
    // Cette fonction sera utile pour afficher les commentaires d'un billet sélectionné.
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Commentaire>
     */
    public function commentaires(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Commentaire::class);
    }

    // Un billet correspond à une ou plusieurs catégories (relation plusieurs à plusieurs).
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Categorie, $this>
     */
    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Categorie::class, 'billet_categorie', 'billet_id', 'categorie_id');
    }

    // Accesseur : permet à un billet de récupérer la liste des libellés de toutes ses catégories.
    // Utilisable via $billet->libelles_categories.
    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<array<int, string>, never>
     */
    protected function libellesCategories(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn (): array => $this->categories->pluck('CAT_LIBELLE')->all(),
        );
    }
}
