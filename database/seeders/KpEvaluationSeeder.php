<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KpEvaluation;
use App\Models\SentimentResult;
use App\Models\Student;
use App\Models\User;

class KpEvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Positive comments dari berbagai evaluator
        $positiveComments = [
            'Mahasiswa sangat rajin dan bertanggung jawab dalam menyelesaikan tugas yang diberikan.',
            'Performa kerja sangat baik, selalu datang tepat waktu dan menunjukkan dedikasi tinggi.',
            'Kemampuan komunikasi dan kerja sama tim sangat memuaskan, selalu proaktif dalam diskusi.',
            'Mahasiswa menunjukkan inisiatif yang baik dan mampu bekerja secara mandiri dengan hasil yang memuaskan.',
            'Sangat cepat belajar dan mudah beradaptasi dengan lingkungan kerja baru. Hasil kerja konsisten bagus.',
            'Attitude sangat positif, ramah dengan semua rekan kerja, dan selalu bersedia membantu.',
            'Mahasiswa memiliki kreativitas tinggi dalam menyelesaikan masalah dan memberikan solusi inovatif.',
            'Penguasaan teknis sangat baik, mampu mengaplikasikan teori yang dipelajari dengan sangat efektif.',
            'Sangat antusias dan bersemangat dalam mengikuti program magang. Selalu aktif bertanya dan belajar.',
            'Hasil pekerjaan selalu melampaui ekspektasi, detail-oriented dan teliti dalam setiap tugas.',
        ];

        // Negative comments dari berbagai evaluator
        $negativeComments = [
            'Mahasiswa sering terlambat dan kurang disiplin dalam mengikuti jadwal kerja yang telah ditentukan.',
            'Perlu peningkatan dalam komunikasi dan koordinasi dengan tim, sering bekerja sendiri tanpa konfirmasi.',
            'Kurang inisiatif dalam mencari tugas baru, cenderung menunggu instruksi dari supervisor.',
            'Hasil kerja masih kurang memuaskan, banyak kesalahan yang perlu diperbaiki berulang kali.',
            'Attitude kurang profesional, terlalu santai dan tidak serius dalam menangani tanggung jawab.',
            'Lambat dalam menyelesaikan tugas dan sering melewati deadline yang telah disepakati.',
            'Kemampuan problem solving masih lemah, kesulitan dalam menangani masalah tanpa bimbingan intensif.',
            'Kurang adaptif dengan budaya kerja perusahaan, sering tidak mengikuti prosedur yang ada.',
            'Perlu banyak perbaikan dalam kualitas kerja, masih banyak kekurangan yang harus diperbaiki.',
            'Tidak konsisten dalam performa, kadang bagus kadang mengecewakan. Perlu lebih fokus.',
        ];

        // Neutral comments dari berbagai evaluator
        $neutralComments = [
            'Mahasiswa menunjukkan performa yang cukup standar, sesuai dengan ekspektasi untuk level magang.',
            'Ada beberapa hal yang perlu ditingkatkan namun secara keseluruhan masih dapat diterima.',
            'Kemampuan teknis cukup baik meskipun masih perlu banyak belajar dan berlatih lebih lanjut.',
            'Cukup kooperatif dalam tim, namun kadang masih perlu diberikan arahan lebih detail.',
            'Hasil kerja standar, tidak ada yang istimewa namun tidak ada masalah signifikan juga.',
            'Mahasiswa cukup rajin namun perlu lebih proaktif dalam mencari pengalaman belajar.',
            'Disiplin waktu cukup baik, meskipun sesekali masih ada keterlambatan minor.',
            'Komunikasi lumayan lancar, tapi masih perlu lebih berani dalam menyampaikan pendapat.',
            'Secara umum memenuhi standar minimum untuk mahasiswa magang di posisi ini.',
            'Ada progress yang terlihat dari awal hingga akhir masa magang, meski masih banyak ruang untuk berkembang.',
        ];

        $students = Student::with(['dosen', 'pembimbingLapangan'])->get();

        foreach ($students as $student) {
            // Evaluasi dari Dosen (jika ada)
            if ($student->dosen) {
                $this->createEvaluation(
                    $student,
                    $student->dosen,
                    'dosen',
                    $positiveComments,
                    $negativeComments,
                    $neutralComments
                );
            }

            // Evaluasi dari Pembimbing Lapangan (jika ada)
            if ($student->pembimbingLapangan) {
                $this->createEvaluation(
                    $student,
                    $student->pembimbingLapangan,
                    'pembimbing_lapangan',
                    $positiveComments,
                    $negativeComments,
                    $neutralComments
                );
            }

            // Evaluasi tambahan dari admin (opsional, 50% chance)
            if (rand(0, 1)) {
                $admin = User::where('role', 'admin')->first();
                if ($admin) {
                    $this->createEvaluation(
                        $student,
                        $admin,
                        'admin',
                        $positiveComments,
                        $negativeComments,
                        $neutralComments
                    );
                }
            }

            // Self evaluation dari mahasiswa sendiri (70% chance)
            if (rand(0, 100) < 70) {
                $this->createEvaluation(
                    $student,
                    $student->user,
                    'mahasiswa',
                    $positiveComments,
                    $negativeComments,
                    $neutralComments
                );
            }
        }
    }

    private function createEvaluation($student, $evaluator, $role, $positiveComments, $negativeComments, $neutralComments)
    {
        // Random sentiment distribution: 50% positive, 30% neutral, 20% negative
        $rand = rand(1, 100);

        if ($rand <= 50) {
            // Positive
            $sentiment = 'positive';
            $comment = $positiveComments[array_rand($positiveComments)];
            $rating = rand(8, 10);
            $score = rand(7500, 9999) / 10000; // 0.7500 - 0.9999
        } elseif ($rand <= 80) {
            // Neutral
            $sentiment = 'neutral';
            $comment = $neutralComments[array_rand($neutralComments)];
            $rating = rand(5, 7);
            $score = rand(4000, 7499) / 10000; // 0.4000 - 0.7499
        } else {
            // Negative
            $sentiment = 'negative';
            $comment = $negativeComments[array_rand($negativeComments)];
            $rating = rand(1, 4);
            $score = rand(1000, 3999) / 10000; // 0.1000 - 0.3999
        }

        // Create evaluation
        $evaluation = KpEvaluation::create([
            'student_id' => $student->id,
            'evaluator_id' => $evaluator->id,
            'evaluator_role' => $role,
            'rating' => $rating,
            'comment_text' => $comment,
            'created_at' => now()->subDays(rand(0, 60)),
        ]);

        // Create sentiment result
        SentimentResult::create([
            'kp_evaluation_id' => $evaluation->id,
            'sentiment_label' => $sentiment,
            'sentiment_score' => $score,
        ]);
    }
}
