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
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $kaprodi = User::create([
            'name' => 'Kaprodi User',
            'email' => 'kaprodi@example.com',
            'password' => bcrypt('password'),
            'role' => 'kaprodi',
        ]);

        // App Settings
        \App\Models\AppSetting::updateOrCreate(['key' => 'app_name'], ['value' => 'SENTIMENT']);
        \App\Models\AppSetting::updateOrCreate(['key' => 'app_logo'], ['value' => 'SENTIMENT']);
        \App\Models\AppSetting::updateOrCreate(['key' => 'maintenance_mode'], ['value' => '0']);
        \App\Models\AppSetting::updateOrCreate(['key' => 'hf_api_url'], ['value' => 'https://router.huggingface.co/hf-inference/models/w11wo/indonesian-roberta-base-sentiment-classifier']);
        \App\Models\AppSetting::updateOrCreate(['key' => 'hf_token'], ['value' => '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']);

        $dosen1 = User::create([
            'name' => 'Dr. Ahmad Wijaya',
            'email' => 'dosen1@example.com',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        $dosen2 = User::create([
            'name' => 'Dr. Siti Rahayu',
            'email' => 'dosen2@example.com',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        \App\Models\Company::create(['name' => 'PT Teknologi Nusantara']);
        \App\Models\Company::create(['name' => 'PT Digital Indonesia']);
        \App\Models\Company::create(['name' => 'PT Inovasi Solusi']);

        $pembimbing1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'pembimbing1@example.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_lapangan',
            'company_id' => 1,
        ]);

        $pembimbing2 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'pembimbing2@example.com',
            'password' => bcrypt('password'),
            'role' => 'pembimbing_lapangan',
            'company_id' => 2,
        ]);

        $mahasiswa1User = User::create([
            'name' => 'Andi Pratama',
            'email' => 'mahasiswa1@example.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa2User = User::create([
            'name' => 'Fitri Handayani',
            'email' => 'mahasiswa2@example.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa3User = User::create([
            'name' => 'Rizki Setiawan',
            'email' => 'mahasiswa3@example.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        $student1 = \App\Models\Student::create([
            'user_id' => $mahasiswa1User->id,
            'name' => 'Andi Pratama',
            'nim' => '2020001',
            'dosen_id' => $dosen1->id,
            'pembimbing_lapangan_id' => $pembimbing1->id,
        ]);

        $student2 = \App\Models\Student::create([
            'user_id' => $mahasiswa2User->id,
            'name' => 'Fitri Handayani',
            'nim' => '2020002',
            'dosen_id' => $dosen1->id,
            'pembimbing_lapangan_id' => $pembimbing2->id,
        ]);

        $student3 = \App\Models\Student::create([
            'user_id' => $mahasiswa3User->id,
            'name' => 'Rizki Setiawan',
            'nim' => '2020003',
            'dosen_id' => $dosen2->id,
            'pembimbing_lapangan_id' => $pembimbing1->id,
        ]);

        \App\Models\StudentInternship::create([
            'student_id' => $student1->id,
            'company_id' => 1,
            'pembimbing_lapangan_id' => $pembimbing1->id,
            'start_date' => '2024-08-01',
            'end_date' => '2024-12-31',
        ]);

        \App\Models\StudentInternship::create([
            'student_id' => $student2->id,
            'company_id' => 2,
            'pembimbing_lapangan_id' => $pembimbing2->id,
            'start_date' => '2024-07-01',
            'end_date' => '2024-11-30',
        ]);

        \App\Models\StudentInternship::create([
            'student_id' => $student3->id,
            'company_id' => 3,
            'pembimbing_lapangan_id' => $pembimbing1->id,
            'start_date' => '2024-09-01',
            'end_date' => '2025-01-31',
        ]);

        // Seed evaluations
        $this->call([
            KpEvaluationSeeder::class,
        ]);
    }
}
