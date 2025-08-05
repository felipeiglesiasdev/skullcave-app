<?php

namespace Database\Factories;

use App\Models\Usuario;
use App\Models\Escola;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Professor>
 */
class ProfessorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_usuario' => Usuario::factory()->professor(),
            'id_escola' => Escola::factory(),
            'titulacao' => $this->faker->randomElement([
                'Graduação',
                'Especialização',
                'Mestrado',
                'Doutorado',
                'Pós-Doutorado'
            ]),
        ];
    }

    /**
     * Create a professor with specific usuario.
     */
    public function forUsuario(int $usuarioId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_usuario' => $usuarioId,
        ]);
    }

    /**
     * Create a professor with specific escola.
     */
    public function forEscola(int $escolaId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_escola' => $escolaId,
        ]);
    }

    /**
     * Create a professor without escola (freelancer).
     */
    public function withoutEscola(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_escola' => null,
        ]);
    }

    /**
     * Create a professor with specific titulacao.
     */
    public function withTitulacao(string $titulacao): static
    {
        return $this->state(fn (array $attributes) => [
            'titulacao' => $titulacao,
        ]);
    }

    /**
     * Create a professor with graduacao.
     */
    public function graduacao(): static
    {
        return $this->state(fn (array $attributes) => [
            'titulacao' => 'Graduação',
        ]);
    }

    /**
     * Create a professor with mestrado.
     */
    public function mestrado(): static
    {
        return $this->state(fn (array $attributes) => [
            'titulacao' => 'Mestrado',
        ]);
    }

    /**
     * Create a professor with doutorado.
     */
    public function doutorado(): static
    {
        return $this->state(fn (array $attributes) => [
            'titulacao' => 'Doutorado',
        ]);
    }
}

