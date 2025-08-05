<?php

namespace Database\Factories;

use App\Models\Disciplina;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Topico>
 */
class TopicoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $topicos = [
            'Introdução ao tema',
            'Conceitos básicos',
            'Exercícios práticos',
            'Teoria avançada',
            'Aplicações práticas',
            'Revisão geral',
            'Estudos de caso',
            'Metodologia',
            'Análise crítica',
            'Conclusões'
        ];

        return [
            'id_disciplina' => Disciplina::factory(),
            'nome' => $this->faker->randomElement($topicos),
            'descricao' => $this->faker->paragraph(2),
        ];
    }

    /**
     * Create a topico with specific disciplina.
     */
    public function forDisciplina(int $disciplinaId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_disciplina' => $disciplinaId,
        ]);
    }

    /**
     * Create a topico with specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => $name,
        ]);
    }

    /**
     * Create a topico for matematica.
     */
    public function matematica(): static
    {
        $topicosMat = [
            'Álgebra Linear',
            'Geometria Analítica',
            'Cálculo Diferencial',
            'Estatística Básica',
            'Trigonometria',
            'Funções',
            'Equações'
        ];

        return $this->state(fn (array $attributes) => [
            'nome' => $this->faker->randomElement($topicosMat),
            'descricao' => 'Tópico específico da disciplina de matemática.',
        ]);
    }

    /**
     * Create a topico for português.
     */
    public function portugues(): static
    {
        $topicosPort = [
            'Gramática',
            'Literatura Brasileira',
            'Redação',
            'Interpretação de Texto',
            'Ortografia',
            'Sintaxe',
            'Semântica'
        ];

        return $this->state(fn (array $attributes) => [
            'nome' => $this->faker->randomElement($topicosPort),
            'descricao' => 'Tópico específico da disciplina de português.',
        ]);
    }
}

