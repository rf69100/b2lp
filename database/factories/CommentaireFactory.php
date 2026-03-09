<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Billet;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commentaire>
 */
class CommentaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $billet = Billet::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        
        return [
            'COM_DATE' => now(),
            'COM_CONTENU' => fake()->text(200),
            'billet_id' => $billet?->id ?? Billet::factory(),
            'user_id' => $user?->id ?? User::factory(),
        ];
    }
}