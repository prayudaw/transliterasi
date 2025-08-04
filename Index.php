<?php include 'Proses.php' ?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Arabic Transliteration using LC</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&family=Amiri&display=swap" rel="stylesheet">
  <link rel="icon" href="asset/logo.png">

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

    #keyboard {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
      gap: 8px;
      padding: 10px;
      background-color: #f8f9fa;
      border-radius: 8px;
    }

    .key {
      padding: 15px 5px;
      font-size: 20px;
      text-align: center;
      background-color: #2A5C60;
      border: 1px solid #ced4da;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.2s, box-shadow 0.2s;
      user-select: none;
      color: #FFF;
    }

    .key:hover {
      background-color: #dde1e5;
    }

    .key:active {
      background-color: #d3d9df;
      box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .special-key {
      background-color: #6c757d;
      color: white;
    }

    .special-key:hover {
      background-color: #5a6268;
    }

    .special-key:active {
      background-color: #545b62;
    }

    .space-key {
      grid-column: span 6;
      background-color: #000;
    }

    .backspace-key {
      grid-column: span 2;
      background-color: #000;
    }
  </style>
</head>

<body class="min-h-screen bg-gray-50">

  <!-- Header Gambar Background -->
  <div class="relative w-full h-48 flex items-center justify-center bg-cover bg-center"
    style="background-image: url('asset/quran-and-tafsir.webp');">
    <div class="absolute inset-0  bg-opacity-60"></div>
    <div class="relative z-10 flex w-full max-w-6xl mx-auto justify-between items-center px-6">
      <h1 class="text-black text-xl sm:text-4xl font-extrabold font-montserrat">
        Arabic Transliteration using Library of Congress
      </h1>
      <div class="flex items-center gap-4">
        <!-- <h1 class="text-white text-3xl sm:text-5xl font-extrabold font-arabic text-right">
          رومنة اللغة العربية using Library of Congress
        </h1> -->
        <img src="asset/logo.png" alt="Logo" class="h-20 sm:h-28">
      </div>
    </div>

  </div>

  <!-- Konten Form -->
  <div class="p-6 sm:p-8 w-full max-w-6xl mx-auto">

    <div class="mt-4 p-2 bg-stone-200 border border-blue-200 shadow-inner">
      <p class="text-left text-xs text-gray-600">
        Aplikasi ini digunakan untuk transliterasi teks Arab menggunakan pedoman LC (Library of Congress). Unggah gambar yang berisi teks Arab, atau ketik teks Arab langsung di bawah ini untuk ditransliterasi.
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
          rows="5" dir="rtl" placeholder="...اكتب هنا"></textarea>
        <div id="keyboard">
          <div class="key" data-key="ذ">ذ</div>
          <div class="key" data-key="1">١</div>
          <div class="key" data-key="2">٢</div>
          <div class="key" data-key="3">٣</div>
          <div class="key" data-key="4">٤</div>
          <div class="key" data-key="5">٥</div>
          <div class="key" data-key="6">٦</div>
          <div class="key" data-key="7">٧</div>
          <div class="key" data-key="8">٨</div>
          <div class="key" data-key="9">٩</div>
          <div class="key" data-key="0">٠</div>
          <div class="key" data-key="-">-</div>
          <div class="key" data-key="=">=</div>
          <div class="key special-key backspace-key" data-key="Backspace">Hapus</div>

          <div class="key" data-key="ض">ض</div>
          <div class="key" data-key="ص">ص</div>
          <div class="key" data-key="ث">ث</div>
          <div class="key" data-key="ق">ق</div>
          <div class="key" data-key="ف">ف</div>
          <div class="key" data-key="غ">غ</div>
          <div class="key" data-key="ع">ع</div>
          <div class="key" data-key="ه">ه</div>
          <div class="key" data-key="خ">خ</div>
          <div class="key" data-key="ح">ح</div>
          <div class="key" data-key="ج">ج</div>
          <div class="key" data-key="د">د</div>
          <div class="key" data-key="\">|</div>

          <div class="key" data-key="ش">ش</div>
          <div class="key" data-key="س">س</div>
          <div class="key" data-key="ي">ي</div>
          <div class="key" data-key="ب">ب</div>
          <div class="key" data-key="ل">ل</div>
          <div class="key" data-key="ا">ا</div>
          <div class="key" data-key="ت">ت</div>
          <div class="key" data-key="ن">ن</div>
          <div class="key" data-key="م">م</div>
          <div class="key" data-key="ك">ك</div>
          <div class="key" data-key="ط">ط</div>

          <div class="key" data-key="ئ">ئ</div>
          <div class="key" data-key="ء">ء</div>
          <div class="key" data-key="ؤ">ؤ</div>
          <div class="key" data-key="ر">ر</div>
          <div class="key" data-key="لا">لا</div>
          <div class="key" data-key="ى">ى</div>
          <div class="key" data-key="ة">ة</div>
          <div class="key" data-key="و">و</div>
          <div class="key" data-key="ز">ز</div>
          <div class="key" data-key="ظ">ظ</div>
          <div class="key special-key space-key" data-key=" ">Spasi</div>
        </div>
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
          class="w-72 py-4 px-6 rounded-lg text-white font-bold text-lg transition duration-300 ease-in-out transform hover:scale-105 shadow-lg hover:shadow-xl"
          style="background-color: #28555B;" name="action" value="act_1">
          Transliterasikan Sesuai LC (Library of Congress)
        </button>
        <button type="submit"
          class="w-72 py-4 px-6 rounded-lg text-white font-bold text-lg transition duration-300 ease-in-out transform hover:scale-105 shadow-lg hover:shadow-xl"
          style="background-color: #28555B;" name="action" value="act_2">
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
    document.addEventListener('DOMContentLoaded', function() {
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
    document.addEventListener('DOMContentLoaded', function() {
      const textBtn = document.getElementById('textModeBtn');
      const imageBtn = document.getElementById('imageModeBtn');

      const textInput = document.getElementById('textInputContainer');
      const imageInput = document.getElementById('imageInputContainer');

      textBtn.addEventListener('click', function() {
        textBtn.classList.add('active-mode');
        imageBtn.classList.remove('active-mode');
        textInput.classList.remove('hidden');
        imageInput.classList.add('hidden');
      });

      imageBtn.addEventListener('click', function() {
        imageBtn.classList.add('active-mode');
        textBtn.classList.remove('active-mode');
        imageInput.classList.remove('hidden');
        textInput.classList.add('hidden');
      });
    });


    const textarea = document.getElementById('arabicTextInput');
    const keyboard = document.getElementById('keyboard');

    keyboard.addEventListener('click', function(e) {
      if (e.target.classList.contains('key')) {
        const key = e.target.dataset.key;

        // Fokus ke textarea
        textarea.focus();

        // Dapatkan posisi kursor saat ini
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;

        if (key === 'Backspace') {
          if (start > 0) {
            // Hapus karakter sebelum kursor
            textarea.value = text.substring(0, start - 1) + text.substring(end);
            // Pindahkan kursor
            textarea.selectionStart = textarea.selectionEnd = start - 1;
          }
        } else if (key === ' ') {
          // Masukkan spasi
          textarea.value = text.substring(0, start) + ' ' + text.substring(end);
          textarea.selectionStart = textarea.selectionEnd = start + 1;
        } else {
          // Masukkan karakter Arab atau harakat
          textarea.value = text.substring(0, start) + key + text.substring(end);
          textarea.selectionStart = textarea.selectionEnd = start + key.length;
        }
      }
    });
  </script>

</body>

</html>