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
        // Ambil semua kontrak yang sudah ada
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

            $occupantData = [
                'full_name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'whatsapp_number' => '62' . fake()->numerify('8##########'),
                'gender' => fake()->randomElement(['male', 'female']), // Tambahkan gender
                'identity_card_file' => 'occupant/dummy_identity.pdf',
                'community_card_file' => fake()->optional()->passthrough('occupant/dummy_community.pdf'),
                'is_student' => $isStudent,
                'student_id' => $isStudent ? fake()->numerify('130######') : null,
                'faculty' => $isStudent ? $faculty : null,
                'study_program' => $isStudent ? $studyProgram : null,
                'class_year' => $isStudent ? $classYear : null,
                'agree_to_regulations' => true,
                'notes' => fake()->optional()->text(100), // Tambahkan notes
                'status' => 'pending_verification',
            ];

            // Buat penghuni utama untuk kontrak ini
            $primaryOccupant = Occupant::create($occupantData);

            // Hubungkan penghuni utama dengan kontrak tanpa flag is_pic di pivot
            $contract->occupants()->attach($primaryOccupant->id);

            // Set PIC di kolom 'contract_pic' pada tabel 'contracts'
            $contract->contract_pic = $primaryOccupant->id;
            $contract->save(); // Penting: Simpan perubahan pada kontrak

            // Opsional: Tambah beberapa penghuni lain (non-PIC) ke kontrak ini
            $additionalOccupantsCount = rand(0, $contract->unit->capacity - 1); // Maksimal kapasitas unit - 1 (karena 1 sudah PIC)
            for ($j = 0; $j < $additionalOccupantsCount; $j++) {
                $additionalOccupantData = [
                    'full_name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'whatsapp_number' => '62' . fake()->numerify('8##########'),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'identity_card_file' => 'occupant/dummy_identity.pdf',
                    'community_card_file' => fake()->optional()->passthrough('occupant/dummy_community.pdf'),
                    'is_student' => fake()->boolean(30), // Mungkin juga mahasiswa
                    'student_id' => null, // Atur null untuk kesederhanaan, bisa ditambahkan logic lebih lanjut
                    'faculty' => null,
                    'study_program' => null,
                    'class_year' => null,
                    'agree_to_regulations' => true,
                    'notes' => fake()->optional()->text(100),
                    'status' => fake()->randomElement(['verified', 'pending_verification']),
                ];
                $additionalOccupant = Occupant::create($additionalOccupantData);
                $contract->occupants()->attach($additionalOccupant->id); // Attach tanpa is_pic
            }
        }
    }
}
