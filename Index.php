<?php include 'Proses.php' ?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Arabic Transliteration using LC</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&family=Amiri&display=swap" rel="stylesheet">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
    }

    .font-arabic {
      font-family: 'Amiri', 'Noto Naskh Arabic', serif;
    }

    .font-montserrat {
      font-family: 'Montserrat', sans-serif;
    }

    .active {
      background-color: #e6fffa;
      border-color: #14b8a6;
      color: #0f766e;
    }
    .active-mode {
  background-color: #fffbeb;
  border-color: #fbbf24;
  color: #92400e;
  box-shadow: 0 0 0 2px #facc15 inset;
}

  </style>
</head>

<body class="min-h-screen bg-gray-50">

  <!-- Header Gambar Background -->
  <div class="relative w-full h-48 flex items-center justify-center bg-cover bg-center"
    style="background-image: url('asset/header-book.jpg');">
    <div class="absolute inset-0 bg-black bg-opacity-60"></div>
    <div class="relative z-10 flex w-full max-w-6xl mx-auto justify-between items-center px-6">
      <h1 class="text-white text-xl sm:text-5xl font-extrabold font-montserrat">
        Arabic Transliteration using LC
      </h1>
      <h1 class="text-white text-3xl sm:text-5xl font-extrabold font-arabic text-right">
        رومنة اللغة العربية using LC
      </h1>
    </div>
  </div>

  <!-- Konten Form -->
  <div class="p-6 sm:p-8 w-full max-w-6xl mx-auto">

    <!-- <h2 class="text-xl sm:text-2xl font-semibold text-center text-orange-500 mb-8">
      (Dari Gambar atau Teks)
    </h2> -->

    <div class="mt-4 p-2 bg-stone-200 border border-blue-200 shadow-inner">
    <p class="text-left text-xs text-gray-600">
      Aplikasi ini digunakan untuk transliterasi teks Arab menggunakan pedoman LC (Library of Congress). Unggah foto yang berisi teks Arab, atau ketik teks Arab langsung di bawah ini untuk ditransliterasi.
    </p>
    </div>

    <!-- Error -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error): ?>
      <div class="mt-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-center">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>
    
    <form action="" method="POST" enctype="multipart/form-data">

      <!-- Tombol Mode -->
      <div class="flex justify-left mb-8 mt-4">
        <button type="button" id="imageModeBtn" 
        class="px-6 py-3 bg-amber-50 border border-gray-300 text-gray-700 font-semibold flex items-center gap-2 bg-white hover:bg-amber-200 hover:border-amber-300 transition duration-200 shadow-sm">
        <span class="text-xl">🖼️</span> <span>Gambar</span>
    </button>
    
    <button type="button" id="textModeBtn"
    class="px-6 py-3 bg-amber-50 border border-gray-300 text-gray-700 font-semibold flex items-center gap-2 bg-white hover:bg-amber-200 hover:border-amber-300 transition duration-200 shadow-sm">
    <span class="text-xl">📝</span> <span>Teks</span>
    </button>
    </div>


      <!-- Input Text -->
      <div id="textInputContainer" class="mb-6 hidden">
        <label for="arabicTextInput" class="block text-lg font-semibold text-gray-700 mb-2">Teks Arab:</label>
        <textarea id="arabicTextInput" name="arabic_text"
          class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 text-right font-arabic"
          rows="5" dir="rtl"></textarea>
      </div>

      <!-- Upload Gambar -->
      <div id="imageInputContainer" class="mb-6">
        <label for="imageUpload" class="block text-lg font-semibold text-gray-700 mb-2">Unggah Gambar:</label>
        <input type="file" id="imageUpload" name="uploaded_image"
          class="w-full p-3 border border-gray-300 rounded-lg">
          
        <div id="imagePreview" class="mt-4 hidden">
          <img src="#" alt="Pratinjau Gambar" class="max-w-full h-auto rounded-lg shadow-md border border-gray-200">
        </div>
      </div>

      <!-- Tombol Submit -->
      <div class="flex justify-center gap-4 mt-8">
        <button type="submit"
          class="w-72 py-4 px- rounded-lg text-white font-bold text-lg transition duration-300 ease-in-out transform hover:scale-105 bg-orange-950 hover:bg-amber-900 shadow-lg hover:shadow-xl"
          name="action" value="act_1">
          Transliterasikan Sesuai LC (Library of Congress)
        </button>
        <button type="submit"
          class="w-72 py-4 px-6 rounded-lg text-white font-bold text-lg transition duration-300 ease-in-out transform hover:scale-105 bg-orange-950 hover:bg-amber-900 shadow-lg hover:shadow-xl"
          name="action" value="act_2">
          Transliterasikan Sesuai LC (Library of Congress) tanpa Diakritik
        </button>
      </div>

    </form>

    <!-- Hasil -->
    <?php if ($transliteratedText): ?>
      <div class="mt-8 p-6 bg-amber-100 border border-blue-200 rounded-lg shadow-inner">
        <h2 class="text-xl font-bold text-indigo-600 mb-3">Hasil Transliterasi:</h2>
        <p class="text-gray-800 text-lg break-words">
          <?php echo htmlspecialchars($transliteratedText); ?>
        </p>
      </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="mt-8 text-sm text-gray-500 text-center">
      <p>Catatan: Aplikasi ini menggunakan Google Gemini API untuk transliterasi.</p>
      <p>ID Aplikasi: <?php echo isset($GLOBALS['__app_id']) ? htmlspecialchars($GLOBALS['__app_id']) : 'N/A'; ?></p>
    </div>

  </div>

  <!-- Script -->
  <script>
    // Pratinjau gambar
    document.getElementById('imageUpload').addEventListener('change', function(event) {
      const [file] = event.target.files;
      const preview = document.getElementById('imagePreview');
      const img = preview.querySelector('img');

      if (file) {
        img.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
      } else {
        preview.classList.add('hidden');
        img.src = '#';
      }
    });

    // Mode Switching + default aktif Images
    document.addEventListener('DOMContentLoaded', function () {
      const textBtn = document.getElementById('textModeBtn');
      const imageBtn = document.getElementById('imageModeBtn');
      const textInput = document.getElementById('textInputContainer');
      const imageInput = document.getElementById('imageInputContainer');

      function activateTextMode() {
        textBtn.classList.add('active');
        imageBtn.classList.remove('active');
        textInput.classList.remove('hidden');
        imageInput.classList.add('hidden');
      }

      function activateImageMode() {
        imageBtn.classList.add('active');
        textBtn.classList.remove('active');
        imageInput.classList.remove('hidden');
        textInput.classList.add('hidden');
      }

      textBtn.addEventListener('click', activateTextMode);
      imageBtn.addEventListener('click', activateImageMode);

      // Aktifkan Images default
      activateImageMode();
    });
    document.addEventListener('DOMContentLoaded', function () {
  const textBtn = document.getElementById('textModeBtn');
  const imageBtn = document.getElementById('imageModeBtn');

  const textInput = document.getElementById('textInputContainer');
  const imageInput = document.getElementById('imageInputContainer');

  textBtn.addEventListener('click', function () {
    textBtn.classList.add('active-mode');
    imageBtn.classList.remove('active-mode');
    textInput.classList.remove('hidden');
    imageInput.classList.add('hidden');
  });

  imageBtn.addEventListener('click', function () {
    imageBtn.classList.add('active-mode');
    textBtn.classList.remove('active-mode');
    imageInput.classList.remove('hidden');
    textInput.classList.add('hidden');
  });
});

  </script>

</body>
</html>