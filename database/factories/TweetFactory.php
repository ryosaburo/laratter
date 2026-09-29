<?php

namespace Database\Factories;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tweet>
 */
class TweetFactory extends Factory
{
  // 🔽 追加
  protected $model = Tweet::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    // 🔽 追加
    return [
      'user_id' => User::factory(), // UserモデルのFactoryを使用してユーザを生成
      'tweet' => $this->faker->text(200) // ダミーのテキストデータ
    ];
  }
}
