<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan - IT Helpdesk PTPN IV</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <script>
        const theme = localStorage.getItem('theme') ?? 'system';
        if (
            theme === 'dark' ||
            (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @filamentStyles
    
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100">

<body class="bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300">

    <div class="absolute top-4 right-4 z-50">
        <button 
            type="button" 
            onclick="toggleTheme()"
            class="p-2 rounded-full bg-white dark:bg-gray-800 text-yellow-500 dark:text-yellow-300 shadow-md hover:shadow-lg transition-all duration-300 focus:outline-none ring-1 ring-gray-200 dark:ring-gray-700"
            title="Ganti Tema"
        >
            <svg class="hidden dark:block w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <svg class="block dark:hidden w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
        </button>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            
            if (html.classList.contains('dark')) {
                // Jika sedang Gelap -> Ubah ke Terang
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                // Jika sedang Terang -> Ubah ke Gelap
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script> 

    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                IT Helpdesk PTPN IV
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                Silakan isi formulir di bawah untuk melaporkan kendala IT.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10 border-t-4 border-blue-600">
                
                <!-- Panggil Livewire Component -->
                <livewire:laporan-form />

            </div>
            <p class="mt-4 text-center text-xs text-gray-400 dark:text-gray-500">
                &copy; {{ date('Y') }} IT Helpdesk PTPN IV. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Filament Scripts -->
    @filamentScripts
</body>
</html>