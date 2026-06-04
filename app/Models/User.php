<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    // Les deux rôles possibles d'un utilisateur.
    public const ROLE_ADMIN = 'admin';   // Administrateur du blog : peut créer/modifier/supprimer des billets.

    public const ROLE_CLIENT = 'client'; // Client : peut seulement consulter les billets et les commenter.

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Indique si l'utilisateur est l'administrateur du blog.
     * Utilisé par BilletPolicy pour autoriser les opérations CRUD sur les billets.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
