<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan - IT Helpdesk PTPN IV</title>
    <!-- Fonts Local / System Fallback -->
    
    <script>
        const theme = localStorage.getItem('user-theme') ?? 'system';
        if (
            theme === 'dark' ||
            (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @filamentStyles
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

        /* Garis proses tak tentu pada layar pemrosesan laporan */
        @keyframes garis-proses {
            0%   { transform: translateX(-110%); }
            100% { transform: translateX(310%); }
        }

        @media (prefers-reduced-motion: reduce) {
            .animate-fade-in-up { animation: none; }
        }

        /* ==== Perbesaran & perapian tipografi form Filament (khusus tampilan, tidak mengubah logika) ==== */
        .fi-section {
            border-radius: 1rem !important;
        }
        .fi-section-header-heading {
            font-size: 1.1875rem !important;
            font-weight: 700 !important;
            letter-spacing: -0.01em;
        }
        .fi-section-header-description {
            font-size: 0.9375rem !important;
            line-height: 1.5rem !important;
        }
        .fi-fo-field-wrp-label span {
            font-size: 0.9375rem !important;
            font-weight: 600 !important;
        }
        .fi-input,
        .fi-input-wrp input,
        .fi-input-wrp textarea,
        .fi-input-wrp select,
        .choices__inner,
        .choices__list--dropdown .choices__item,
        .fi-fo-rich-editor .ql-editor {
            font-size: 1rem !important;
        }
        .fi-input-wrp {
            border-radius: 0.75rem !important;
        }
        .fi-fo-field-wrp-error-message {
            font-size: 0.875rem !important;
            font-weight: 500;
        }
        .fi-fieldset,
        .fi-section-content-ctn {
            row-gap: 1.5rem !important;
        }
    </style>
</head>
<body class="bg-[#F5F6F8] dark:bg-gray-950 transition-colors duration-300 relative overflow-x-hidden">

    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative z-10 animate-fade-in-up">

        <div class="sm:mx-auto sm:w-full sm:max-w-2xl px-4 sm:px-0 mb-6">
            <div class="flex justify-between items-start">
                <div class="flex items-center space-x-4">
                    <!-- Brand Icon -->
                    <div class="flex-shrink-0">
                        <!-- Logo Updated -->
                        <img src="{{ asset('img/logo-ptpn.png') }}" alt="PTPN Logo" class="h-14 w-auto">
                    </div>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white">
                            IT Helpdesk
                        </h2>
                        <p class="mt-0.5 text-sm font-medium text-gray-500 dark:text-gray-400">
                            PTPN IV
                        </p>
                    </div>
                </div>

                <!-- Theme Toggle Button -->
                <button
                    type="button"
                    onclick="toggleTheme()"
                    class="p-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    title="Ganti Tema"
                >
                    <!-- Sun Icon -->
                    <svg class="hidden dark:block w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <!-- Moon Icon -->
                    <svg class="block dark:hidden w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>
            </div>
            
            <p class="mt-4 text-base leading-relaxed text-gray-600 dark:text-gray-400">
                Ada kendala teknis? Silakan isi formulir di bawah ini dan tim kami akan segera membantu Anda.
            </p>
        </div>

        <div class="mt-2 sm:mx-auto sm:w-full sm:max-w-2xl">
            <div class="bg-white dark:bg-gray-900 py-8 px-4 shadow-sm sm:rounded-2xl sm:px-10 border border-gray-200 dark:border-gray-800">
                <!-- Panggil Livewire Component -->
                <livewire:laporan-form />
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">
                    &copy; {{ date('Y') }} IT Helpdesk PTPN IV.
                </p>
            </div>
        </div>
    </div>

    <!-- Script Theme Toggle -->
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('user-theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('user-theme', 'dark');
            }
        }
    </script>

    <!-- Filament Scripts -->
    @filamentScripts
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
                fail(({ status, content, preventDefault }) => {
                    if (status === 419 || status === 404) {
                        alert('Maaf, sesi Anda telah berakhir atau terjadi kesalahan koneksi. Halaman akan dimuat ulang untuk memperbarui data.');
                        window.location.reload();
                        preventDefault();
                    }
                })
            })
        });
    </script>
</body>
</html>