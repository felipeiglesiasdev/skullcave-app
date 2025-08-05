<?php

namespace Database\Factories;

use App\Models\DisciplinaProfessor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TopicoProfessor>
 */
class TopicoProfessorFactory extends Factory
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
            'Conceitos fundamentais',
            'Exercícios dirigidos',
            'Teoria aplicada',
            'Casos práticos',
            'Revisão do conteúdo',
            'Avaliação diagnóstica',
            'Metodologia ativa',
            'Análise de resultados',
            'Síntese final'
        ];

        return [
            'id_disciplina_professor' => DisciplinaProfessor::factory(),
            'nome' => $this->faker->randomElement($topicos),
            'descricao' => $this->faker->paragraph(2),
        ];
    }

    /**
     * Create a topico_professor with specific disciplina_professor.
     */
    public function forDisciplinaProfessor(int $disciplinaProfessorId): static
    {
        return $this->state(fn (array $attributes) => [
            'id_disciplina_professor' => $disciplinaProfessorId,
        ]);
    }

    /**
     * Create a topico_professor with specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => $name,
        ]);
    }

    /**
     * Create a topico_professor for matematica.
     */
    public function matematica(): static
    {
        $topicosMat = [
            'Números e Operações',
            'Álgebra Básica',
            'Geometria Plana',
            'Estatística Descritiva',
            'Funções Lineares',
            'Equações do 1º Grau',
            'Proporções e Regra de Três'
        ];

        return $this->state(fn (array $attributes) => [
            'nome' => $this->faker->randomElement($topicosMat),
            'descricao' => 'Tópico de matemática desenvolvido pelo professor para a turma.',
        ]);
    }

    /**
     * Create a topico_professor for português.
     */
    public function portugues(): static
    {
        $topicosPort = [
            'Leitura e Interpretação',
            'Produção Textual',
            'Gramática Aplicada',
            'Literatura Nacional',
            'Análise Linguística',
            'Comunicação Oral',
            'Variações Linguísticas'
        ];

        return $this->state(fn (array $attributes) => [
            'nome' => $this->faker->randomElement($topicosPort),
            'descricao' => 'Tópico de português desenvolvido pelo professor para a turma.',
        ]);
    }
}

