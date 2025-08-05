<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Escola>
 */
class EscolaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->company() . ' - Escola',
            'cnpj' => $this->generateCNPJ(),
            'endereco' => $this->faker->address(),
            'telefone' => $this->faker->phoneNumber(),
        ];
    }

    /**
     * Generate a valid CNPJ format.
     */
    private function generateCNPJ(): string
    {
        // Gera um CNPJ no formato XX.XXX.XXX/XXXX-XX
        $cnpj = $this->faker->numerify('##.###.###/####-##');
        return $cnpj;
    }

    /**
     * Create a escola with specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'nome' => $name,
        ]);
    }

    /**
     * Create a escola with specific CNPJ.
     */
    public function withCNPJ(string $cnpj): static
    {
        return $this->state(fn (array $attributes) => [
            'cnpj' => $cnpj,
        ]);
    }
}

