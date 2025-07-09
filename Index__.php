<?php


// Fungsi untuk mendapatkan kunci API Gemini.
// Dalam lingkungan produksi, Anda harus menggunakan variabel lingkungan atau sistem manajemen rahasia.
// Untuk demo ini, kita akan mengasumsikan kunci API disediakan secara otomatis oleh lingkungan Canvas.
function getGeminiApiKey()
{
    // Highly recommended: Use environment variables for API keys in production
    // For local testing, you might hardcode it here
    return getenv('GEMINI_API_KEY') ?: 'AIzaSyB4s_G6UN8r_MzjdVIn0LlJMN5-yvDlXAs'; // Replace with your actual API key
}

// Fungsi untuk memanggil Gemini API
function transliterateText($arabicText)
{
    $apiKey = getGeminiApiKey();

    if (empty($apiKey)) {
        return "Error: Gemini API key is not set. Please get one from Google AI Studio.";
    }



    // Gemini API endpoint for text generation (gemini-pro model)
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

    // Construct the request payload in the format expected by Gemini API
    $payload = [
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => 'Transliterasikan teks Arab berikut ke Latin sesuai standar Library of Congress:' . $arabicText]
                ]
            ]
        ]
    ];

    // Encode the payload as JSON
    $jsonPayload = json_encode($payload);

    // Initialize cURL session
    $ch = curl_init($apiUrl);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as a string
    curl_setopt($ch, CURLOPT_POST, true);           // Set request method to POST
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload); // Set JSON payload
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonPayload)
    ]);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "cURL Error: " . $error_msg;
    }

    // Get HTTP status code
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Close cURL session
    curl_close($ch);

    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Handle API errors based on HTTP status code and response body
    if ($http_code !== 200) {
        $error_message = isset($responseData['error']['message']) ? $responseData['error']['message'] : 'Unknown API error.';
        return "API Error ($http_code): " . $error_message;
    }

    // Extract the generated text from the response
    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        return trim($responseData['candidates'][0]['content']['parts'][0]['text']);
    } else {
        return "Error: Unexpected API response format or no generated text found.";
    }
}

// Tangani permintaan POST dari formulir
$arabicText = '';
$transliteratedText = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $arabicText = isset($_POST['arabic_text']) ? trim($_POST['arabic_text']) : '';

    if (empty($arabicText)) {
        $error = 'Harap masukkan teks Arab untuk ditransliterasi.';
    } else {

        $transliteratedText = transliterateText($arabicText);

        if (strpos($transliteratedText, 'Error:') === 0) {
            $error = $transliteratedText;
            $transliteratedText = ''; // Kosongkan hasil jika ada error
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Transliterasi Arab Sederhana</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Custom font for Arabic text input, if needed */
        .font-arabic {
            font-family: 'Amiri', 'Noto Naskh Arabic', serif;
            /* Contoh font Arabic */
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4 sm:p-6 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl p-6 sm:p-8 w-full max-w-2xl border border-blue-200">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-center text-indigo-700 mb-6">
            Aplikasi Transliterasi Arab
        </h1>

        <p class="text-center text-gray-600 mb-8">
            Masukkan teks Arab di bawah ini dan biarkan AI mentransliterasikannya ke dalam aksara Latin menggunakan standar LC.
        </p>

        <form method="POST" action="">
            <div class="mb-6">
                <label for="imageUpload" class="block text-lg font-semibold text-gray-700 mb-2">
                    Unggah Foto (Opsional):
                </label>
                <input
                    type="file"
                    id="imageUpload"
                    name="uploaded_image"
                    accept="image/*"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition duration-200 ease-in-out file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                <p class="text-sm text-gray-500 mt-2">Format yang didukung: JPG, PNG, GIF.</p>
                <br />
                <label for="arabicInput" class="block text-lg font-semibold text-gray-700 mb-2">
                    Teks Arab:
                </label>
                <textarea
                    id="arabicInput"
                    name="arabic_text"
                    class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-200 ease-in-out text-right text-xl font-arabic"
                    rows="5"
                    placeholder="اكتب نصاً عربياً هنا..."
                    dir="rtl"><?php echo htmlspecialchars($arabicText); ?></textarea>
            </div>

            <button
                type="submit"
                class="w-full py-3 px-6 rounded-lg text-white font-bold text-lg transition duration-300 ease-in-out transform hover:scale-105 bg-indigo-600 hover:bg-indigo-700 shadow-lg hover:shadow-xl">
                Transliterasi
            </button>
        </form>

        <?php if ($error): ?>
            <div class="mt-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($transliteratedText): ?>
            <div class="mt-8 p-6 bg-blue-50 border border-blue-200 rounded-lg shadow-inner">
                <h2 class="text-xl font-bold text-indigo-600 mb-3">Hasil Transliterasi:</h2>
                <p class="text-gray-800 text-lg break-words">
                    <?php echo htmlspecialchars($transliteratedText); ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="mt-8 text-sm text-gray-500 text-center">
            <p>Catatan: Aplikasi ini menggunakan Google Gemini API untuk transliterasi.</p>
            <p>ID Aplikasi: <?php echo isset($GLOBALS['__app_id']) ? htmlspecialchars($GLOBALS['__app_id']) : 'N/A'; ?></p>
        </div>
    </div>
</body>

</html>