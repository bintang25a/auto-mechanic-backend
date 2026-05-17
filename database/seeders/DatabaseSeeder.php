<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create();
        User::factory()->staff()->create();
        User::factory()->mechanic()->create();

        User::factory(40)->create();

        User::factory(8)->unverified()->create();
        User::factory(8)->mechanical()->create();

        $this->call([
            SymptomSeeder::class,
            DamageSeeder::class,
            RuleSeeder::class,
        ]);
    }
}
