<?php

namespace Database\Factories;

use App\Models\Flashcard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PerguntaFlashcard>
 */
class PerguntaFlashcardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_flashcard' => Flashcard::factory(),
            'pergunta' => $this->faker->sentence() . '?',
            'resposta' => $this->faker->paragraph(2),
        ];
    }

    /**
     * Create a pergunta_flashcard with specific flashcard.
     */
    public function forFlashcard(int $flashcardId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_flashcard' => $flashcardId,
        ]);
    }

    /**
     * Create a pergunta_flashcard with specific question.
     */
    public function withQuestion(string $pergunta, string $resposta): static
    {
        return $this->state(fn (array $attributes) => [
            'pergunta' => $pergunta,
            'resposta' => $resposta,
        ]);
    }

    /**
     * Create a pergunta_flashcard for matematica.
     */
    public function matematica(): static
    {
        $perguntas = [
            [
                'pergunta' => 'Qual é a fórmula para calcular a área de um triângulo?',
                'resposta' => 'A área de um triângulo é calculada pela fórmula: A = (base × altura) / 2'
            ],
            [
                'pergunta' => 'Como resolver uma equação do 1º grau?',
                'resposta' => 'Para resolver uma equação do 1º grau, isole a variável realizando operações inversas em ambos os lados da equação.'
            ],
            [
                'pergunta' => 'O que é o Teorema de Pitágoras?',
                'resposta' => 'O Teorema de Pitágoras estabelece que em um triângulo retângulo, o quadrado da hipotenusa é igual à soma dos quadrados dos catetos: a² + b² = c²'
            ]
        ];

        $perguntaEscolhida = $this->faker->randomElement($perguntas);

        return $this->state(fn (array $attributes) => [
            'pergunta' => $perguntaEscolhida['pergunta'],
            'resposta' => $perguntaEscolhida['resposta'],
        ]);
    }

    /**
     * Create a pergunta_flashcard for português.
     */
    public function portugues(): static
    {
        $perguntas = [
            [
                'pergunta' => 'Quais são as classes gramaticais variáveis?',
                'resposta' => 'As classes gramaticais variáveis são: substantivo, adjetivo, artigo, numeral, pronome e verbo.'
            ],
            [
                'pergunta' => 'O que é uma figura de linguagem?',
                'resposta' => 'Figura de linguagem é um recurso expressivo usado para dar maior expressividade ao texto, criando efeitos de sentido especiais.'
            ],
            [
                'pergunta' => 'Quando usar a crase?',
                'resposta' => 'A crase é usada quando há fusão da preposição "a" com o artigo definido "a" ou com pronomes demonstrativos que iniciam com "a".'
            ]
        ];

        $perguntaEscolhida = $this->faker->randomElement($perguntas);

        return $this->state(fn (array $attributes) => [
            'pergunta' => $perguntaEscolhida['pergunta'],
            'resposta' => $perguntaEscolhida['resposta'],
        ]);
    }

    /**
     * Create a simple pergunta_flashcard.
     */
    public function simple(): static
    {
        return $this->state(fn (array $attributes) => [
            'pergunta' => 'Pergunta simples?',
            'resposta' => 'Resposta direta e objetiva.',
        ]);
    }

    /**
     * Create a complex pergunta_flashcard.
     */
    public function complex(): static
    {
        return $this->state(fn (array $attributes) => [
            'pergunta' => $this->faker->paragraph(1) . '?',
            'resposta' => $this->faker->paragraph(4),
        ]);
    }
}

