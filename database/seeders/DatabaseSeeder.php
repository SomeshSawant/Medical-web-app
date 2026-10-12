<?php

namespace Database\Seeders;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Demo account passwords matching Jajal Medical assignment requirements
        $adminPassword = env('SEED_ADMIN_PASSWORD', 'Admin@12345');
        $viewerPassword = env('SEED_VIEWER_PASSWORD', 'Viewer@12345');
        $engineerPassword = env('SEED_ENGINEER_PASSWORD', 'Engineer@12345');

        // Required Demo Accounts
        $admin = User::updateOrCreate(
            ['email' => 'admin@jajalmedical.test'],
            [
                'name' => 'Dr. Robert Carter (Admin)',
                'password' => Hash::make($adminPassword),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        $viewer = User::updateOrCreate(
            ['email' => 'viewer@jajalmedical.test'],
            [
                'name' => 'Auditor Sarah Lynn (Viewer)',
                'password' => Hash::make($viewerPassword),
                'role' => User::ROLE_VIEWER,
                'email_verified_at' => now(),
            ]
        );

        $engineer = User::updateOrCreate(
            ['email' => 'engineer@jajalmedical.test'],
            [
                'name' => 'Marcus Vance (Engineer)',
                'password' => Hash::make($engineerPassword),
                'role' => User::ROLE_ENGINEER,
                'email_verified_at' => now(),
            ]
        );

        // Fallback demo accounts for backward compatibility
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Dr. Robert Carter (Admin Legacy)',
                'password' => Hash::make($adminPassword),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'viewer@example.com'],
            [
                'name' => 'Auditor Sarah Lynn (Viewer Legacy)',
                'password' => Hash::make($viewerPassword),
                'role' => User::ROLE_VIEWER,
                'email_verified_at' => now(),
            ]
        );

        // Predefined initial cases across all statuses to ensure immediate dashboard demonstration
        $curatedCases = [
            [
                'case_number' => 'CASE-1001',
                'patient_reference' => 'PAT-NX401',
                'surgeon_name' => 'Dr. Eleanor Vance',
                'implant_type' => 'Titanium Hip Stem',
                'status' => MedicalCase::STATUS_PLANNING,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->addDays(5)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1002',
                'patient_reference' => 'PAT-KZ912',
                'surgeon_name' => 'Dr. Julian Thorne',
                'implant_type' => 'Acetabular Cup System',
                'status' => MedicalCase::STATUS_IN_REVIEW,
                'priority' => MedicalCase::PRIORITY_MEDIUM,
                'surgery_date' => now()->addDays(12)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1003',
                'patient_reference' => 'PAT-BL310',
                'surgeon_name' => 'Dr. Marcus Webb',
                'implant_type' => 'Cobalt-Chrome Femoral Knee',
                'status' => MedicalCase::STATUS_APPROVED,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->addDays(3)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-1004',
                'patient_reference' => 'PAT-TR884',
                'surgeon_name' => 'Dr. Sophia Reyes',
                'implant_type' => 'Porous Coated Tibial Tray',
                'status' => MedicalCase::STATUS_COMPLETED,
                'priority' => MedicalCase::PRIORITY_MEDIUM,
                'surgery_date' => now()->subDays(4)->format('Y-m-d'),
                'created_by' => $admin->id,
            ],
            [
                'case_number' => 'CASE-1005',
                'patient_reference' => 'PAT-GH552',
                'surgeon_name' => 'Dr. Eleanor Vance',
                'implant_type' => 'Spine Lumbar Interbody Cage',
                'status' => MedicalCase::STATUS_DRAFT,
                'priority' => MedicalCase::PRIORITY_LOW,
                'surgery_date' => now()->addDays(20)->format('Y-m-d'),
                'created_by' => $engineer->id,
            ],
            [
                'case_number' => 'CASE-1006',
                'patient_reference' => 'PAT-QM719',
                'surgeon_name' => 'Dr. Arthur Pendelton',
                'implant_type' => 'Anterior Cervical Disc Prosthesis',
                'status' => MedicalCase::STATUS_CANCELLED,
                'priority' => MedicalCase::PRIORITY_LOW,
                'surgery_date' => null,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($curatedCases as $caseData) {
            MedicalCase::firstOrCreate(
                ['case_number' => $caseData['case_number']],
                $caseData
            );
        }

        // Generate additional dummy cases using the factory to total at least 25 cases
        $userIds = [$admin->id, $engineer->id];
        $additionalCount = 20;

        for ($i = 0; $i < $additionalCount; $i++) {
            MedicalCase::factory()->create([
                'created_by' => $userIds[array_rand($userIds)],
            ]);
        }
    }
}
