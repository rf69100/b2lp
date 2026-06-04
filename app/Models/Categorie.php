<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    /** @use HasFactory<\Database\Factories\CategorieFactory> */
    use HasFactory;

    protected $fillable = [
        'CAT_LIBELLE',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot',
    ];

    // Une catégorie correspond à plusieurs billets (relation plusieurs à plusieurs).
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Billet, $this>
     */
    public function billets(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Billet::class, 'billet_categorie', 'categorie_id', 'billet_id');
    }
}
