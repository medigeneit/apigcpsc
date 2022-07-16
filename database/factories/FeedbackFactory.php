<?php

namespace Database\Factories;

use App\Models\Feedback;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Type\Integer;

class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = Feedback::class;

    public function definition()
    {
        return [
            // 'fq_id' => random_int(1,2),
            'fq_id' => 1,
            // 'email' => $this->faker->unique()->safeEmail(),
            // 'email_verified_at' => now(),

            'appointment_id' =>$this->faker->unique()->numberBetween(1,10000000),
            // 'mentor_id' => random_int(1,10),
            'ratings' => [random_int(1,5),random_int(1,5),random_int(1,5),random_int(1,5),random_int(1,5)],
            'note' => Str::random(100)
        ];
    }
}
