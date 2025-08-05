<?php

namespace Database\Factories;

use App\Models\Turma;
use App\Models\Aluno;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TurmaAluno>
 */
class TurmaAlunoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_turma' => Turma::factory(),
            'id_usuario_aluno' => Aluno::factory(),
        ];
    }

    /**
     * Create a turma_aluno with specific turma.
     */
    public function forTurma(int $turmaId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_turma' => $turmaId,
        ]);
    }

    /**
     * Create a turma_aluno with specific aluno.
     */
    public function forAluno(int $alunoId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_usuario_aluno' => $alunoId,
        ]);
    }
}

