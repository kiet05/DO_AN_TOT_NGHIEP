<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Banner;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'    => $this->faker->sentence(3),
            // lưu chuỗi path ảnh (giống dữ liệu bạn đang có: banners/xxxx.jpg)
            'image'    => 'banners/' . $this->faker->unique()->lexify('????????') . '.jpg',
            'link'     => $this->faker->optional(0.6)->url(),              // có thể null
            'position' => $this->faker->randomElement(['top','middle','bottom']),
            'status'   => $this->faker->boolean(90) ? 1 : 0,               // 1: bật, 0: tắt
        ];
    }

    /** Banner đang bật */
    public function active(): self
    {
        return $this->state(fn () => ['status' => 1]);
    }

    /** Banner đang tắt */
    public function inactive(): self
    {
        return $this->state(fn () => ['status' => 0]);
    }

    /** Vị trí tiện lợi */
    public function top(): self    { return $this->state(fn () => ['position' => 'top']); }
    public function middle(): self { return $this->state(fn () => ['position' => 'middle']); }
    public function bottom(): self { return $this->state(fn () => ['position' => 'bottom']); }
}
