<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Un administrateur du blog, avec des identifiants connus pour les tests/démo.
        // Seul cet utilisateur pourra créer/modifier/supprimer des billets.
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@lyonpalme.fr',
            'password' => Hash::make('password'),
        ]);

        // Deux clients « classiques » (lecture des billets + commentaires uniquement).
        User::factory(2)->create();
    }
}
