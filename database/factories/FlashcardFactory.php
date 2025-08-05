<?php

namespace Database\Factories;

use App\Models\Topico;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flashcard>
 */
class FlashcardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_topico' => Topico::factory(),
            'titulo' => $this->faker->sentence(4),
            'descricao' => $this->faker->paragraph(2),
            'data_criacao' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Create a flashcard with specific topico.
     */
    public function forTopico(int $topicoId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_topico' => $topicoId,
        ]);
    }

    /**
     * Create a flashcard with specific title.
     */
    public function withTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'titulo' => $title,
        ]);
    }

    /**
     * Create a flashcard for matematica.
     */
    public function matematica(): static
    {
        $titulos = [
            'Equações do 1º Grau',
            'Teorema de Pitágoras',
            'Regra de Três',
            'Frações e Decimais',
            'Área e Perímetro',
            'Funções Lineares',
            'Estatística Básica'
        ];

        return $this->state(fn (array $attributes) => [
            'titulo' => $this->faker->randomElement($titulos),
            'descricao' => 'Flashcard com conceitos importantes de matemática.',
        ]);
    }

    /**
     * Create a flashcard for português.
     */
    public function portugues(): static
    {
        $titulos = [
            'Classes Gramaticais',
            'Figuras de Linguagem',
            'Concordância Verbal',
            'Análise Sintática',
            'Gêneros Textuais',
            'Acentuação Gráfica',
            'Crase'
        ];

        return $this->state(fn (array $attributes) => [
            'titulo' => $this->faker->randomElement($titulos),
            'descricao' => 'Flashcard com conceitos importantes de português.',
        ]);
    }

    /**
     * Create a recent flashcard.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'data_criacao' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}

