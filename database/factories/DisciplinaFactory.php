<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Disciplina>
 */
class DisciplinaFactory extends Factory
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
            'id_usuario' => Usuario::factory(),
            'nome' => $this->faker->randomElement($disciplinas),
            'descricao' => $this->faker->paragraph(3),
        ];
    }

    /**
     * Create a disciplina with specific usuario.
     */
    public function forUsuario(int $usuarioId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_usuario' => $usuarioId,
        ]);
    }

    /**
     * Create a disciplina with specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => $name,
        ]);
    }

    /**
     * Create a disciplina for matematica.
     */
    public function matematica(): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => 'Matemática',
            'descricao' => 'Disciplina focada no ensino de conceitos matemáticos fundamentais.',
        ]);
    }

    /**
     * Create a disciplina for português.
     */
    public function portugues(): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => 'Português',
            'descricao' => 'Disciplina voltada para o ensino da língua portuguesa e literatura.',
        ]);
    }

    /**
     * Create a disciplina for ciências.
     */
    public function ciencias(): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => 'Ciências',
            'descricao' => 'Disciplina que aborda conceitos básicos de física, química e biologia.',
        ]);
    }
}

