<?php

namespace Database\Factories;

use App\Models\Escola;
use App\Models\Professor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Turma>
 */
class TurmaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ano = $this->faker->numberBetween(1, 3);
        $serie = $this->faker->randomElement(['A', 'B', 'C', 'D']);
        $periodo = $this->faker->randomElement(['Manhã', 'Tarde', 'Noite']);
        
        return [
            'id_escola' => Escola::factory(),
            'id_professor' => Professor::factory(),
            'nome_turma' => "{$ano}º Ano {$serie} - {$periodo}",
        ];
    }

    /**
     * Create a turma with specific escola.
     */
    public function forEscola(int $escolaId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_escola' => $escolaId,
        ]);
    }

    /**
     * Create a turma with specific professor.
     */
    public function forProfessor(int $professorId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_professor' => $professorId,
        ]);
    }

    /**
     * Create a turma with specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'nome_turma' => $name,
        ]);
    }

    /**
     * Create a turma for ensino fundamental.
     */
    public function ensinoFundamental(): static
    {
        $ano = $this->faker->numberBetween(1, 9);
        $serie = $this->faker->randomElement(['A', 'B', 'C']);
        
        return $this->state(fn (array $attributes) => [
            'nome_turma' => "{$ano}º Ano {$serie} - Fundamental",
        ]);
    }

    /**
     * Create a turma for ensino médio.
     */
    public function ensinoMedio(): static
    {
        $ano = $this->faker->numberBetween(1, 3);
        $serie = $this->faker->randomElement(['A', 'B', 'C']);
        
        return $this->state(fn (array $attributes) => [
            'nome_turma' => "{$ano}º Ano {$serie} - Médio",
        ]);
    }
}

