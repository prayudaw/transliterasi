<?php

include 'Constants.php';


// Fungsi untuk mendapatkan kunci API Gemini.
// Dalam lingkungan produksi, Anda harus menggunakan variabel lingkungan atau sistem manajemen rahasia.
// Untuk demo ini, kita akan mengasumsikan kunci API disediakan secara otomatis oleh lingkungan Canvas.
function getGeminiApiKey()
{
    return getenv('GEMINI_API_KEY') ?: GEMINI_API_KEY; // Replace with your actual API key
}

/**
 * Fungsi untuk memanggil Gemini API (baik Flash untuk teks atau Pro-Vision untuk gambar).
 *
 * @param string $promptText Teks prompt untuk dikirim ke Gemini.
 * @param string|null $base64ImageData Data gambar dalam format Base64 (opsional).
 * @return string Hasil transliterasi atau pesan error.
 */
function callGeminiAPI($promptText, $base64ImageData = null)
{
    $apiKey = getGeminiApiKey();

    if (empty($apiKey)) {
        return "Error: Kunci API tidak tersedia. Harap pastikan GEMINI_API_KEY diatur.";
    }

    #$modelId = 'gemini-2.5-pro'; // Default untuk teks
    $modelId = 'gemini-2.5-flash'; // Default untuk teks

    $contents = [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $promptText]
            ]
        ]
    ];

    // Jika ada data gambar, gunakan model vision dan tambahkan gambar ke payload
    if ($base64ImageData !== null) {
        #$modelId = 'gemini-2.5-pro'; // Model untuk pemahaman gambar
        $modelId = 'gemini-2.5-flash'; // Default untuk teks
        $contents[0]['parts'][] = [
            'inlineData' => [
                'mimeType' => 'image/jpeg', // Sesuaikan mimeType jika Anda mendukung format lain
                'data' => $base64ImageData
            ]
        ];
    }
    // $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$modelId}:generateContent?key={$apiKey}";

    $payload = ['contents' => $contents];
    // var_dump($contents);
    // die();
    $jsonPayload = json_encode($payload);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonPayload)
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // if (curl_errno($ch)) {
    //     return "Error cURL: " . curl_error($ch);
    // }

    $responseData = json_decode($response, true);

    if ($http_code !== 200) {
        $error_message = isset($responseData['error']['message']) ? $responseData['error']['message'] : 'Kesalahan tidak diketahui dari API.';
        return "Error API ($http_code): " . $error_message;
    }

    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        return trim($responseData['candidates'][0]['content']['parts'][0]['text']);
    } else {
        return "Error: Format respons API tidak terduga atau tidak ada hasil transliterasi.";
    }
}

// Tangani permintaan POST dari formulir
$arabicText = '';
$transliteratedText = '';
$error = '';
$uploadedImageBase64 = null;
$imagePreviewSrc = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'act_1') {  // proses transliterasi sesuai LC 
            $arabicText = isset($_POST['arabic_text']) ? trim($_POST['arabic_text']) : '';
            $hasImageUpload = isset($_FILES['uploaded_image']) && $_FILES['uploaded_image']['error'] === UPLOAD_ERR_OK;


            if (empty($arabicText) && !$hasImageUpload) {
                $error = 'Harap masukkan teks Arab atau unggah foto.';
            } else {
                $prompt = "Transliterate the following Arabic text into Latin script using the Library of Congress (LC) standard. Provide only the transliterated text, without any additional explanations or formatting.";

                if ($hasImageUpload) {
                    $imageFileType = strtolower(pathinfo($_FILES['uploaded_image']['name'], PATHINFO_EXTENSION));
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];



                    if (!in_array($imageFileType, $allowedTypes)) {
                        $error = "Format file gambar tidak didukung. Harap unggah JPG, PNG, atau GIF.";
                    } else {
                        $imageData = file_get_contents($_FILES['uploaded_image']['tmp_name']);
                        if ($imageData === false) {
                            $error = "Gagal membaca file gambar yang diunggah.";
                        } else {
                            $uploadedImageBase64 = base64_encode($imageData);

                            $imagePreviewSrc = 'data:' . mime_content_type($_FILES['uploaded_image']['tmp_name']) . ';base64,' . $uploadedImageBase64;

                            // Prompt untuk Gemini Vision API
                            // $prompt = "Extract all Arabic text from this image and then transliterate it into Latin script using the Library of Congress (LC) standard. Provide only the transliterated text, without any additional explanations or formatting.";
                            $prompt = "Transliterasikan teks Arab pada gambar ini ke Latin sesuai standar Library of Congress";
                            $transliteratedText = callGeminiAPI($prompt, $uploadedImageBase64);
                        }
                    }
                } else {
                    // Jika tidak ada gambar, proses teks dari textarea
                    $transliteratedText = callGeminiAPI($prompt . "\n\n" . $arabicText);
                }

                if (strpos($transliteratedText, 'Error:') === 0) {
                    $error = $transliteratedText;
                    $transliteratedText = ''; // Kosongkan hasil jika ada error
                }
            }
        } else {   // proses transliterasi sesuai LC  Tanpa diakritik

            $arabicText = isset($_POST['arabic_text']) ? trim($_POST['arabic_text']) : '';
            $hasImageUpload = isset($_FILES['uploaded_image']) && $_FILES['uploaded_image']['error'] === UPLOAD_ERR_OK;


            if (empty($arabicText) && !$hasImageUpload) {
                $error = 'Harap masukkan teks Arab atau unggah foto.';
            } else {
                $prompt = "transliterasi text ini dalam bahasa Latin standar LC tanpa diakritik";

                if ($hasImageUpload) {
                    $imageFileType = strtolower(pathinfo($_FILES['uploaded_image']['name'], PATHINFO_EXTENSION));
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];



                    if (!in_array($imageFileType, $allowedTypes)) {
                        $error = "Format file gambar tidak didukung. Harap unggah JPG, PNG, atau GIF.";
                    } else {
                        $imageData = file_get_contents($_FILES['uploaded_image']['tmp_name']);
                        if ($imageData === false) {
                            $error = "Gagal membaca file gambar yang diunggah.";
                        } else {
                            $uploadedImageBase64 = base64_encode($imageData);

                            $imagePreviewSrc = 'data:' . mime_content_type($_FILES['uploaded_image']['tmp_name']) . ';base64,' . $uploadedImageBase64;

                            // Prompt untuk Gemini Vision API
                            // $prompt = "Extract all Arabic text from this image and then transliterate it into Latin script using the Library of Congress (LC) standard. Provide only the transliterated text, without any additional explanations or formatting.";
                            $prompt = "Transliterasikan teks Arab pada gambar ini ke Latin sesuai standar Library of Congress tanpa diakritik";
                            $transliteratedText = callGeminiAPI($prompt, $uploadedImageBase64);
                        }
                    }
                } else {
                    // Jika tidak ada gambar, proses teks dari textarea
                    $transliteratedText = callGeminiAPI($prompt . "\n\n" . $arabicText);
                }

                if (strpos($transliteratedText, 'Error:') === 0) {
                    $error = $transliteratedText;
                    $transliteratedText = ''; // Kosongkan hasil jika ada error
                }
            }
            # code...
        }
    }
}
