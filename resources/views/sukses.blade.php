<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Berhasil - IT Helpdesk</title>
    <!-- Replace CDN with Local Vite Assets for Offline Support -->
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
        }
        @media (prefers-reduced-motion: reduce) {
            .animate-fade-in-up { animation: none; }
        }
    </style>
</head>
<body class="bg-[#F5F6F8] dark:bg-gray-950 transition-colors duration-300 relative overflow-x-hidden min-h-screen flex items-center justify-center font-sans selection:bg-blue-500 selection:text-white">

    <div class="w-full max-w-lg px-4 py-12 relative z-10 animate-fade-in-up">

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-8 sm:p-10 text-center">
            <!-- Logo -->
            <div class="mb-8 flex justify-center">
                <img src="{{ asset('img/logo-ptpn.png') }}" alt="PTPN Logo" class="h-14 w-auto">
            </div>

            <!-- Success Icon -->
            <div class="mx-auto w-14 h-14 bg-green-50 dark:bg-green-950/40 border border-green-100 dark:border-green-900/50 rounded-full flex items-center justify-center mb-6">
                <svg class="w-7 h-7 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white mb-3">
                Laporan Diterima
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-base mb-6 leading-relaxed">
                Terima kasih, tim IT Support kami akan segera menindaklanjuti kendala Anda.
            </p>

            <!-- Ringkasan Tiket -->
            <dl class="mb-6 divide-y divide-gray-100 dark:divide-gray-800 rounded-xl border border-gray-200 dark:border-gray-800 text-left">
                <div class="flex items-center justify-between gap-4 px-4 py-3">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Nomor Tiket</dt>
                    <dd class="text-sm font-medium font-mono text-gray-900 dark:text-white">{{ $ticket->no_tiket }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 px-4 py-3">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Kategori</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white text-right">{{ $ticket->topik_bantuan }}</dd>
                </div>
                @if($ticket->level_urgensi)
                    <div class="flex items-center justify-between gap-4 px-4 py-3">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Tingkat Urgensi</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->level_urgensi }}</dd>
                    </div>
                @endif
                <div class="flex items-center justify-between gap-4 px-4 py-3">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-600 dark:text-amber-500">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                        Dalam Antrean
                    </dd>
                </div>
            </dl>

            <p class="mb-6 text-xs leading-relaxed text-gray-400 dark:text-gray-500 text-left">
                Kategori dan tingkat urgensi ditentukan otomatis dari isi laporan Anda, dan dapat disesuaikan kembali oleh tim IT Support saat pengecekan.
            </p>

            <!-- Link Tiket -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4 mb-6 text-left">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Simpan link tiket Anda</p>

                <div class="relative flex items-center">
                    <input type="text" id="ticketLink" readonly
                        value="{{ route('laporan.cek', ['uuid' => $ticket->uuid]) }}"
                        class="block w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-sm font-mono py-2.5 pl-3 pr-11 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 cursor-pointer"
                        onclick="this.select();">

                    <button onclick="copyLink()" class="absolute right-1.5 p-1.5 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" title="Salin Link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
                <div id="copyMsg" class="mt-2 text-green-600 dark:text-green-400 text-xs opacity-0 transition-opacity duration-300 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Link berhasil disalin
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ route('laporan.cek', ['uuid' => $ticket->uuid]) }}"
                   class="block w-full py-3 px-4 rounded-lg bg-blue-600 text-white font-semibold text-base shadow-sm hover:bg-blue-700 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                    Buka Halaman Tiket
                </a>

                <a href="{{ route('home') }}"
                   class="block w-full py-3 px-4 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-medium text-base hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400">
                   Kembali ke Beranda
                </a>
            </div>

            <p class="mt-8 text-sm text-gray-400 dark:text-gray-500">
                &copy; {{ date('Y') }} IT Helpdesk PTPN IV
            </p>
        </div>
    </div>

    <script>
        function copyLink() {
            var copyText = document.getElementById("ticketLink");
            copyText.select();
            copyText.setSelectionRange(0, 99999); 

            navigator.clipboard.writeText(copyText.value).then(function() {
                var msg = document.getElementById("copyMsg");
                msg.classList.remove('opacity-0');
                setTimeout(() => {
                    msg.classList.add('opacity-0');
                }, 3000);
            }, function(err) {
                alert('Gagal menyalin link. Silakan salin manual.');
            });
        }
    </script>

</body>
</html>