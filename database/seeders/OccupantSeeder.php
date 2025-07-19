<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Occupant;
use App\Models\Contract;
use App\Enums\OccupantStatus;
use App\Data\AcademicData;

class OccupantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contracts = Contract::all();
        if ($contracts->isEmpty()) {
            $this->command->info('Tidak ada kontrak yang tersedia untuk membuat data penghuni.');
            return;
        }

        foreach ($contracts as $contract) {
            $isStudent = fake()->boolean(70); // 70% kemungkinan adalah mahasiswa
            $faculty = fake()->randomElement(array_keys(AcademicData::getFacultiesAndPrograms()));
            $studyProgram = fake()->randomElement(AcademicData::getFacultiesAndPrograms()[$faculty]);
            $classYear = fake()->numberBetween(date('Y') - 4, date('Y'));


            $occupant = Occupant::create([
                'full_name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'whatsapp_number' => '62' . fake()->numerify('8##########'),
                'identity_card_file' => 'occupant/dummy_identity.pdf',
                'community_card_file' => fake()->optional()->passthrough('occupant/dummy_community.pdf'),
                'is_student' => $isStudent,
                'student_id' => $isStudent ? fake()->numerify('130######') : null,
                'faculty' => $isStudent ? $faculty : null,
                'study_program' => $isStudent ? $studyProgram : null,
                'class_year' => $isStudent ? $classYear : null,
                'agree_to_regulations' => true,
                'status' => 'pending_verification',
            ]);

            // Hubungkan occupant dengan kontrak
            $contract->occupants()->attach($occupant->id, ['is_pic' => true]);
        }
    }
}