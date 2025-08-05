<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Independente>
 */
class IndependenteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_usuario' => Usuario::factory()->independente(),
        ];
    }

    /**
     * Create an independente with specific usuario.
     */
    public function forUsuario(int $usuarioId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_usuario' => $usuarioId,
        ]);
    }
}

