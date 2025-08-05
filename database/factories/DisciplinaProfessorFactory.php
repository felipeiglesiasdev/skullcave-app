<?php

namespace Database\Factories;

use App\Models\Turma;
use App\Models\Professor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DisciplinaProfessor>
 */
class DisciplinaProfessorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $disciplinas = [
            'Matemática',
            'Português',
            'História',
            'Geografia',
            'Ciências',
            'Física',
            'Química',
            'Biologia',
            'Inglês',
            'Educação Física',
            'Artes',
            'Filosofia',
            'Sociologia',
            'Literatura',
            'Redação'
        ];

        return [
            'id_turma' => Turma::factory(),
            'id_professor' => Professor::factory(),
            'nome' => $this->faker->randomElement($disciplinas),
            'descricao' => $this->faker->paragraph(2),
        ];
    }

    /**
     * Create a disciplina_professor with specific turma.
     */
    public function forTurma(int $turmaId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_turma' => $turmaId,
        ]);
    }

    /**
     * Create a disciplina_professor with specific professor.
     */
    public function forProfessor(int $professorId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_professor' => $professorId,
        ]);
    }

    /**
     * Create a disciplina_professor with specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => $name,
        ]);
    }

    /**
     * Create a disciplina_professor for matematica.
     */
    public function matematica(): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => 'Matemática',
            'descricao' => 'Disciplina de matemática ministrada pelo professor para esta turma.',
        ]);
    }

    /**
     * Create a disciplina_professor for português.
     */
    public function portugues(): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => 'Português',
            'descricao' => 'Disciplina de português ministrada pelo professor para esta turma.',
        ]);
    }
}

