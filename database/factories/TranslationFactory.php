<?php

namespace Database\Factories\InternetGuru\LaravelTranslatable\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use InternetGuru\LaravelTranslatable\Models\Translation;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition()
    {
        return [
            'locale' => $this->faker->locale(),
            'attribute' => $this->faker->word(),
            'value' => $this->faker->sentence(),
        ];
    }
}
