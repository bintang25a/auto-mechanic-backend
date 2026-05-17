<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Complaint>
 */
class ComplaintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Membuat string nomor komplain acak yang unik, misal: CMP-20260517-XYZ
        $complaintNumber = 'CMP-'.now()->format('Ymd').'-'.strtoupper($this->faker->lexify('???'));

        return [
            'complaint_number' => $complaintNumber,
            'customer_id' => User::query()->where('role', 'customer')->inRandomOrder()->first()?->uid ?? User::factory()->create(['role' => 'customer'])->uid,
            'queue_id' => Queue::factory(),
            'vehicle' => $this->faker->randomElement(['Honda Vario 150', 'Yamaha NMAX', 'Suzuki Satria FU', 'Honda Beat']),
            'license_number' => 'B '.$this->faker->numberBetween(1000, 9999).' '.strtoupper($this->faker->lexify('??')),
            'description' => $this->faker->sentence(6),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
