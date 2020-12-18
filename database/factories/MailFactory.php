<?php

namespace Database\Factories;

use App\Models\Mail;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Mail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'to'      => $this->faker->email,
            'from'    => $this->faker->email,
            'subject' => $this->faker->title,
            'content' => $this->faker->text(250),
        ];
    }

    public function onlyRequired()
    {
        return $this->state(
            function (array $attributes) {
                return [
                    'subject' => null,
                    'content' => null,
                ];
            }
        );
    }

    public function created()
    {
        return $this->state(
            function (array $attributes) {
                return [
                    'state' => Mail::STATE_CREATED,
                ];
            }
        );
    }
}
