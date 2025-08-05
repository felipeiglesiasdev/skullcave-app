<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'senha' => Hash::make('password'), // senha padrão
            'tipo' => $this->faker->randomElement(['admin', 'professor', 'aluno', 'independente']),
            'data_cadastro' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is a professor.
     */
    public function professor(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'professor',
        ]);
    }

    /**
     * Indicate that the user is an aluno.
     */
    public function aluno(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'aluno',
        ]);
    }

    /**
     * Indicate that the user is an independente.
     */
    public function independente(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'independente',
        ]);
    }

    /**
     * Indicate that the user email should be verified.
     */
    public function emailVerified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the user email should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

