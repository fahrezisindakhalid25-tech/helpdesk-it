<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Berhasil</title>
    <!-- Replace CDN with Local Vite Assets for Offline Support -->
    <!-- CDN Assets for Full Online Appearance -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Fonts Local / System Fallback -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg border border-gray-200 text-center m-4">
        
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">Terima Kasih!</h1>
        <p class="text-gray-500 mb-8">Laporan berhasil dikirim. Silakan <b>Simpan Link</b> di bawah ini untuk memantau status atau membalas pesan Admin.</p>

        <div class="mb-8 text-left">
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1 ml-1">Link Tiket Anda:</label>
            <div class="flex shadow-sm rounded-md">
                <input type="text" id="ticketLink" readonly
                    value="{{ route('laporan.cek', ['uuid' => $ticket->uuid]) }}"
                    class="flex-1 block w-full rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm p-3 focus:ring-0 focus:outline-none truncate">
                
                <button onclick="copyLink()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-r-md bg-blue-600 text-white hover:bg-blue-700 font-bold text-sm transition focus:outline-none">
                    📋 Salin
                </button>
            </div>
            <p id="copyMsg" class="text-green-600 text-xs mt-2 font-bold hidden text-center">✅ Link berhasil disalin ke clipboard!</p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('laporan.cek', ['uuid' => $ticket->uuid]) }}" class="block w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-md hover:bg-blue-700 transition shadow-md">
                🚀 Buka Tiket Sekarang
            </a>
            
            <a href="{{ route('home') }}" class="block w-full bg-gray-100 text-gray-600 font-bold py-3 px-4 rounded-md border border-gray-300 hover:bg-gray-200 transition">
                Kembali ke Form Utama
            </a>
        </div>
    </div>

    <script>
        function copyLink() {
            var copyText = document.getElementById("ticketLink");
            
            copyText.select();
            copyText.setSelectionRange(0, 99999); 

            navigator.clipboard.writeText(copyText.value).then(function() {
                var msg = document.getElementById("copyMsg");
                msg.classList.remove('hidden');
                setTimeout(() => {
                    msg.classList.add('hidden');
                }, 3000);
            }, function(err) {
                alert('Gagal menyalin link. Silakan salin manual.');
            });
        }
    </script>

</body>
</html>