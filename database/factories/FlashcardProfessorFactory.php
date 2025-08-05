<?php

namespace Database\Factories;

use App\Models\TopicoProfessor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlashcardProfessor>
 */
class FlashcardProfessorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_topico_professor' => TopicoProfessor::factory(),
            'titulo' => $this->faker->sentence(4),
            'descricao' => $this->faker->paragraph(2),
            'data_criacao' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Create a flashcard_professor with specific topico_professor.
     */
    public function forTopicoProfessor(int $topicoProfessorId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_topico_professor' => $topicoProfessorId,
        ]);
    }

    /**
     * Create a flashcard_professor with specific title.
     */
    public function withTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'titulo' => $title,
        ]);
    }

    /**
     * Create a flashcard_professor for matematica.
     */
    public function matematica(): static
    {
        $titulos = [
            'Resolução de Problemas',
            'Conceitos Fundamentais',
            'Exercícios Práticos',
            'Aplicações do Dia a Dia',
            'Revisão para Prova',
            'Dicas de Estudo',
            'Exemplos Resolvidos'
        ];

        return $this->state(fn (array $attributes) => [
            'titulo' => $this->faker->randomElement($titulos),
            'descricao' => 'Flashcard criado pelo professor para auxiliar no ensino de matemática.',
        ]);
    }

    /**
     * Create a flashcard_professor for português.
     */
    public function portugues(): static
    {
        $titulos = [
            'Técnicas de Redação',
            'Interpretação Textual',
            'Regras Gramaticais',
            'Literatura Brasileira',
            'Comunicação Eficaz',
            'Análise de Textos',
            'Produção Textual'
        ];

        return $this->state(fn (array $attributes) => [
            'titulo' => $this->faker->randomElement($titulos),
            'descricao' => 'Flashcard criado pelo professor para auxiliar no ensino de português.',
        ]);
    }

    /**
     * Create a recent flashcard_professor.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'data_criacao' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Create an old flashcard_professor.
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'data_criacao' => $this->faker->dateTimeBetween('-2 years', '-6 months'),
        ]);
    }
}

