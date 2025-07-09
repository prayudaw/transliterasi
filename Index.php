<?php include 'Proses.php' ?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Transliterasi Arab (Gambar & Teks)</title>
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

<body class="min-h-screen bg-gradient-to-br from-green-50 to-teal-100 p-4 sm:p-6 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl p-6 sm:p-8 w-full max-w-2xl border border-green-200">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-center text-teal-700 mb-6">
            Aplikasi Transliterasi Arab
        </h1>
        <h2 class="text-xl sm:text-2xl font-semibold text-center text-teal-500 mb-8">
            (Dari Gambar atau Teks)
        </h2>

        <p class="text-center text-gray-600 mb-8">
            Unggah foto yang berisi teks Arab, atau ketik teks Arab langsung di bawah ini untuk ditransliterasi.
        </p>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-6">
                <label for="imageUpload" class="block text-lg font-semibold text-gray-700 mb-2">
                    Unggah Foto (Opsional):
                </label>
                <input
                    type="file"
                    id="imageUpload"
                    name="uploaded_image"
                    accept="image/jpeg, image/png, image/gif"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition duration-200 ease-in-out file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                <p class="text-sm text-gray-500 mt-2">Format yang didukung: JPG, PNG, GIF.</p>
                <div id="imagePreview" class="mt-4 <?php echo $imagePreviewSrc ? '' : 'hidden'; ?>">
                    <img src="<?php echo htmlspecialchars($imagePreviewSrc ?: '#'); ?>" alt="Pratinjau Gambar" class="max-w-full h-auto rounded-lg shadow-md border border-gray-200">
                </div>
            </div>

            <div class="mb-6">
                <label for="arabicTextInput" class="block text-lg font-semibold text-gray-700 mb-2">
                    Atau Ketik Teks Arab di Sini (Opsional):
                </label>
                <textarea
                    id="arabicTextInput"
                    name="arabic_text"
                    class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition duration-200 ease-in-out text-right text-xl font-arabic"
                    rows="5"
                    placeholder="اكتب نصاً عربياً هنا..."
                    dir="rtl"><?php echo htmlspecialchars($arabicText); ?></textarea>
            </div>

            <div class="flex gap-x-4">
                <button
                    type=" submit"
                    class="w-1/2 py-4 px- rounded-lg text-white font-bold text-lg transition duration-300 ease-in-out transform hover:scale-105 bg-teal-600 hover:bg-teal-700 shadow-lg hover:shadow-xl" name="action" value="act_1">
                    Transliterasikan Sesuai LC (Library of Congress)
                </button>
                <button
                    type=" submit"
                    class="w-1/2 py-4 px-6 rounded-lg text-white font-bold text-lg transition duration-300 ease-in-out transform hover:scale-105 bg-teal-600 hover:bg-teal-700 shadow-lg hover:shadow-xl" name="action" value="act_2">
                    Transliterasikan Sesuai LC (Library of Congress) tanpa Diakritik
                </button>
            </div>



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

    <script>
        // JavaScript untuk menampilkan pratinjau gambar yang diunggah
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                const preview = document.getElementById('imagePreview');
                const img = preview.querySelector('img');
                img.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            } else {
                document.getElementById('imagePreview').classList.add('hidden');
                document.getElementById('imagePreview').querySelector('img').src = '#';
            }
        });

        // Pastikan pratinjau gambar tetap terlihat jika ada gambar yang diunggah sebelumnya (setelah refresh/submit)
        document.addEventListener('DOMContentLoaded', function() {
            const imagePreview = document.getElementById('imagePreview');
            const img = imagePreview.querySelector('img');
            if (img.src && img.src !== window.location.href + '#') { // Check if src is not empty or default
                imagePreview.classList.remove('hidden');
            }
        });
    </script>
</body>

</html>