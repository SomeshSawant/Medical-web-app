<?php

namespace Database\Factories;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalCase>
 */
class MedicalCaseFactory extends Factory
{
    protected $model = MedicalCase::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $implantTypes = [
            'Titanium Hip Stem',
            'Acetabular Cup System',
            'Cobalt-Chrome Femoral Knee',
            'Porous Coated Tibial Tray',
            'Spine Lumbar Interbody Cage',
            'Anterior Cervical Disc Prosthesis',
            'Anatomical Bone Plate & Screws',
            'Dual-Chamber Pacemaker Lead',
            'Bioresorbable Vascular Scaffold',
            'Shoulder Reverse Glenosphere',
        ];

        return [
            'case_number' => 'CASE-' . str_pad((string) fake()->unique()->numberBetween(1001, 9999), 4, '0', STR_PAD_LEFT),
            'patient_reference' => 'PAT-' . strtoupper(fake()->bothify('??###')),
            'surgeon_name' => 'Dr. ' . fake()->name(),
            'implant_type' => fake()->randomElement($implantTypes),
            'status' => fake()->randomElement(MedicalCase::STATUSES),
            'priority' => fake()->randomElement(MedicalCase::PRIORITIES),
            'surgery_date' => fake()->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'created_by' => User::factory(),
        ];
    }
}
