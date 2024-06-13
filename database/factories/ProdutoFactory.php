<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produto>
 */
class ProdutoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->sentence(1),
            'descricao' => fake()->text(15),
            'preco' => fake()->randomFloat(2, 10, 100),
            'categoria_id' => fake()->numberBetween(1, 10)
        ];
    }
}
