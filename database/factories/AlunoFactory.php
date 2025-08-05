<?php

namespace Database\Factories;

use App\Models\Usuario;
use App\Models\Escola;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aluno>
 */
class AlunoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_usuario' => Usuario::factory()->aluno(),
            'id_escola' => Escola::factory(),
            'matricula' => $this->faker->unique()->numerify('ALU######'),
        ];
    }

    /**
     * Create an aluno with specific usuario.
     */
    public function forUsuario(int $usuarioId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_usuario' => $usuarioId,
        ]);
    }

    /**
     * Create an aluno with specific escola.
     */
    public function forEscola(int $escolaId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_escola' => $escolaId,
        ]);
    }

    /**
     * Create an aluno without escola (independente).
     */
    public function withoutEscola(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_escola' => null,
        ]);
    }

    /**
     * Create an aluno with specific matricula.
     */
    public function withMatricula(string $matricula): static
    {
        return $this->state(fn (array $attributes) => [
            'matricula' => $matricula,
        ]);
    }
}

