<?php

namespace App\Policies;

use App\Models\Billet;
use App\Models\User;

class BilletPolicy
{
    /**
     * La consultation de la liste des billets est publique : tout utilisateur peut lister.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * La consultation d'un billet est ouverte à tout utilisateur authentifié.
     */
    public function view(User $user, Billet $billet): bool
    {
        return true;
    }

    /**
     * Seul l'administrateur peut créer un billet.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Seul l'administrateur peut modifier un billet.
     */
    public function update(User $user, Billet $billet): bool
    {
        return $user->isAdmin();
    }

    /**
     * Seul l'administrateur peut supprimer un billet.
     */
    public function delete(User $user, Billet $billet): bool
    {
        return $user->isAdmin();
    }

    /**
     * Seul l'administrateur peut restaurer un billet.
     */
    public function restore(User $user, Billet $billet): bool
    {
        return $user->isAdmin();
    }

    /**
     * Seul l'administrateur peut supprimer définitivement un billet.
     */
    public function forceDelete(User $user, Billet $billet): bool
    {
        return $user->isAdmin();
    }
}
