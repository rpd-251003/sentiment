<?php

$hfToken = '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';

$url = 'https://router.huggingface.co/hf-inference/models/w11wo/indonesian-roberta-base-sentiment-classifier';

$data = [
    "inputs" => "Mahasiswa menjalankan tugas sesuai arahan yang diberikan"
];

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $hfToken,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);

if ($response === false) {
    die('Curl error: ' . curl_error($ch));
}

curl_close($ch);

// Decode JSON response
$result = json_decode($response, true);

// ===============================
// DEBUG: tampilkan hasil mentah
// ===============================
echo "<pre>";
print_r($result);
echo "</pre>";

// ===============================
// AMBIL SENTIMEN UTAMA
// ===============================
$topPrediction = $result[0][0] ?? null;

if (!$topPrediction) {
    die('Invalid sentiment response');
}

$label = $topPrediction['label'];      // positive | neutral | negative
$confidence = $topPrediction['score']; // 0.0 - 1.0

// ===============================
// KONVERSI KE SKALA 1–10
// (makin tinggi makin positif)
// ===============================
$rating10 = match ($label) {
    'positive' => 7 + floor($confidence * 3),          // 7 – 10
    'neutral'  => 5 + floor($confidence * 1),          // 5 – 6
    'negative' => max(1, 4 - floor($confidence * 3)),  // 1 – 4
    default    => 5,
};

// ===============================
// OUTPUT FINAL (SIAP KE DATABASE)
// ===============================
echo "<hr>";
echo "<strong>Final Result</strong><br>";
echo "Sentiment Label : <b>{$label}</b><br>";
echo "Confidence      : " . round($confidence, 4) . "<br>";
echo "Rating (1–10)   : <b>{$rating10}</b><br>";
