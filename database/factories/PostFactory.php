<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create(); 
        $title = $this->faker->sentence(4);
        return [
            'title' => $title,
            'author_id' => $user->id,
            'slug' => Str::slug($title),
            'content' => $this->faker->text(),
            'published_at' => \Carbon\Carbon::now()
        ];
    }
}
