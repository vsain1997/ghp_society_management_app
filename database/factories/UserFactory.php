<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uid' => Str::uuid(),
            'role' => 'super_admin',//$this->faker->randomElement(['super_admin', 'admin', 'resident', 'service_provider']),
            'status' => 'active',//$this->faker->randomElement(['active', 'inactive']),
            'name' => 'Mr. Super',//$this->faker->name(),
            'email' => 'master1@futureprofilez.com',//$this->faker->unique()->safeEmail(),
            'email_verified_at' => now(), // 70% chance that email is verified
            'password' => Hash::make('123456789'), // default password
            'otp' => NULL,//$this->faker->optional()->numerify('######'), // random 6-digit OTP
            'otp_verified_at' => NULL,//$this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'otp_expire_time' => NULL,//$this->faker->optional()->dateTimeBetween('now', '+1 week'),
            'image' => '',//$this->faker->optional()->imageUrl(400, 400, 'people'), // random image URL
            'phone' => '1234567890',//$this->faker->optional()->phoneNumber(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // return [
        //     'name' => fake()->name(),
        //     'email' => fake()->unique()->safeEmail(),
        //     'email_verified_at' => now(),
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        //     'remember_token' => Str::random(10),
        // ];
    }

    /**
     * After creating the user, insert default notification settings if role is super_admin.
     */
    public function configure(): static
    {
        return $this->afterCreating(function ($user) {
            if ($user->role === 'super_admin') {
                // Fetch admin panel notification settings from config
                $adminPanelNotifications = config('notification_settings.super_admin_panel', []);
                $insertDefaultNotificationPanel = [];

                foreach ($adminPanelNotifications as $defaultSettingName) {
                    $insertDefaultNotificationPanel[] = [
                        'name' => $defaultSettingName,
                        'status' => 'enabled',
                        'user_of_system' => 'panel',
                        'user_id' => $user->id,
                        'role' => $user->role,
                        'society_id' => null, // Replace this with appropriate society_id value if needed
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($insertDefaultNotificationPanel)) {
                    \DB::table('notification_settings')->insert($insertDefaultNotificationPanel);
                }
            }
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
