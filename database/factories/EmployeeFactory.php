<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EmployeeFactory extends Factory
{
    
    protected $model = Employee::class;
    
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'image' => $this->faker->imageUrl(),
            'position' => $this->faker->jobTitle,
            'division_id' => Division::inRandomOrder()->first()->id, // Memilih divisi yang sudah ada secara acak
        ];
    }
}
