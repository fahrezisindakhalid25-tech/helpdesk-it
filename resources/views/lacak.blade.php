<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket #{{ $ticket->no_tiket }} - Helpdesk</title>
    
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
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        const theme = localStorage.getItem('user-theme') ?? 'system';
        if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* trix-toolbar [data-trix-button-group="file-tools"] { display: none; } */ /* File attachment enabled */
        .trix-content ul { list-style-type: disc; margin-left: 1em; }
        .trix-content ol { list-style-type: decimal; margin-left: 1em; }
        
        /* Mobile Optimization for Trix */
        @media (max-width: 640px) {
            trix-toolbar .trix-button-group:not(:first-child) {
                display: none; /* Hide advanced tools on mobile */
            }
            trix-toolbar .trix-button-group:first-child {
                margin-bottom: 0;
            }
        }

        /* Dark Mode for Trix */
        .dark trix-editor {
            background-color: #374151; /* gray-700 */
            color: #f3f4f6; /* gray-100 */
            border-color: #4b5563; /* gray-600 */
        }
        .dark trix-toolbar {
            background-color: #1f2937; /* gray-800 */
            border-color: #4b5563;
        }
        .dark trix-toolbar .trix-button {
            background-color: #374151;
            color: white;
        }
        .dark trix-toolbar .trix-button.trix-active {
            background-color: #2563eb; /* blue-600 */
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex flex-col">

    <div class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 sticky top-0 z-10">
        <div class="max-w-3xl mx-auto px-4 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="w-full sm:w-auto">
                <h1 class="text-xl font-bold text-gray-800 dark:text-white truncate">Tiket #{{ $ticket->no_tiket }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 break-words">{{ $ticket->deskripsi_umum_masalah }}</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto mt-2 sm:mt-0">
                <button 
                    type="button" 
                    onclick="toggleTheme()" 
                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-yellow-300 hover:bg-gray-200 dark:hover:bg-gray-600 shadow-sm border border-gray-200 dark:border-gray-600 transition-colors focus:outline-none flex-shrink-0" 
                    title="Ganti Tema">
                    <!-- Sun Icon -->
                    <svg class="hidden dark:block w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <!-- Moon Icon -->
                    <svg class="block dark:hidden w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>

                <div class="flex flex-col items-end">
                    <span class="px-3 py-1 rounded-full text-xs font-bold 
                        {{ $ticket->status == 'Solved' ? 'bg-green-100 text-green-800' : 
                          ($ticket->status == 'Closed' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ $ticket->status }}
                    </span>
                    <a href="{{ route('home') }}" class="text-xs text-blue-600 mt-1 hover:underline">Kembali ke Home</a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-4" id="chat-container-scroll">
        <div class="max-w-3xl mx-auto space-y-6">
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700/50 rounded-lg p-4 text-sm text-yellow-800 dark:text-yellow-200">
                <p><strong>Detail Laporan:</strong></p>
                <div class="mt-1 text-gray-700 dark:text-gray-300">{!! $ticket->penjelasan_lengkap !!}</div>
                
                @if($ticket->gambar)
                    <div class="mt-3">
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Lampiran Gambar:</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(json_decode($ticket->gambar, true) ?? [] as $gambar)
                                <img src="{{ asset('storage/' . $gambar) }}" onclick="openModal(this.src)" alt="Laporan Gambar" class="rounded-lg max-w-xs border border-yellow-300 dark:border-yellow-700 shadow-sm cursor-pointer hover:opacity-90 transition">
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">Dibuat pada: {{ $ticket->created_at->format('d M Y H:i') }}</div>
            </div>

            <!-- CHAT CONTAINER (AJAX TARGET) -->
            <div id="chat-history">
                @include('partials.chat_history')
            </div>

        </div>
    </div>

<div class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 p-4 sticky bottom-0">
        <div class="max-w-3xl mx-auto">

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Gagal!</strong>
                    <span class="block sm:inline">{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            
            @if($isExpired)
                <div class="text-center p-4 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 rounded-lg flex flex-col items-center">
                    <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span class="font-bold">Tiket Ditutup</span>
                    <span class="text-xs">Masa berlaku tiket (5 Hari) telah habis atau masalah dinyatakan selesai permanen.</span>
                </div>

            @elseif($ticket->status === 'Solved')
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-center">
                    <p class="font-bold text-green-800 dark:text-green-300">Masalah Telah Diselesaikan</p>
                    <p class="text-sm text-green-700 dark:text-green-400 mt-1">
                        Jika Anda merasa masalah ini belum tuntas atau muncul kembali, silakan balas pesan di bawah ini untuk membuka kembali tiket.
                    </p>
                </div>

                <form action="{{ route('laporan.reply', $ticket->uuid) }}" method="POST">
                    @csrf
                    <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-start">
                        <div class="flex-1">
                            <input id="x" type="hidden" name="isi_pesan">
                            <trix-editor input="x" placeholder="Tulis balasan untuk membuka kembali tiket..." class="bg-white dark:bg-gray-700 min-h-[80px] rounded-lg"></trix-editor>
                        </div>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition flex items-center justify-center gap-2 h-[40px] w-full sm:w-auto">
                            <span>Re-Open</span>
                        </button>
                    </div>
                </form>

            @elseif(!$adminSudahJawab)
                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 rounded-lg flex flex-col items-center animate-pulse">
                    <svg class="w-8 h-8 mb-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold">Menunggu Respon Admin</span>
                    <span class="text-xs">Anda dapat membalas pesan setelah Admin merespon laporan ini.</span>
                </div>

            @else
                <form action="{{ route('laporan.reply', $ticket->uuid) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex flex-col gap-3">
                        <div class="flex-1">
                            <input id="reply-input" type="hidden" name="isi_pesan">
                            <trix-editor input="reply-input" placeholder="Tulis balasan Anda..." class="bg-white dark:bg-gray-700 min-h-[100px] rounded-lg"></trix-editor>
                        </div>
                        
                        <div class="flex justify-end mt-3">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center justify-center gap-2 h-[40px] w-full sm:w-auto">
                                <span>Kirim Balasan</span>
                            </button>
                        </div>
                    </div>
                </form>

                <script>
                    (function() {
                        var HOST = "/laporan-upload-trix"; // Endpoint upload

                        addEventListener("trix-attachment-add", function(event) {
                            if (event.attachment.file) {
                                uploadFileAttachment(event.attachment)
                            }
                        })

                        function uploadFileAttachment(attachment) {
                            uploadFile(attachment.file, setProgress, setAttributes)

                            function setProgress(progress) {
                                attachment.setUploadProgress(progress)
                            }

                            function setAttributes(attributes) {
                                attachment.setAttributes(attributes)
                            }
                        }

                        function uploadFile(file, progressCallback, successCallback) {
                            var key = createStorageKey(file)
                            var formData = new FormData()
                            var xhr = new XMLHttpRequest()

                            formData.append("file", file)
                            formData.append("_token", "{{ csrf_token() }}") // CSRF Token Laravel

                            xhr.open("POST", HOST, true)

                            xhr.upload.addEventListener("progress", function(event) {
                                var progress = event.loaded / event.total * 100
                                progressCallback(progress)
                            })

                            xhr.addEventListener("load", function(event) {
                                if (xhr.status == 200) {
                                    var response = JSON.parse(xhr.responseText)
                                    successCallback({
                                        url: response.url,
                                        href: response.url
                                    })
                                }
                            })

                            xhr.send(formData)
                        }

                        function createStorageKey(file) {
                            var date = new Date()
                            var day = date.toISOString().slice(0, 10)
                            var name = date.getTime() + "-" + file.name
                            return "tmp/" + day + "/" + name
                        }
                    })();
                </script>
            @endif

        </div>
    </div>

    <!-- Modal Zoom Gambar -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeModal()">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-4xl max-h-[90vh] flex flex-col">
            <!-- Header Modal -->
            <div class="flex justify-between items-center p-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Pratinjau Gambar</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            
            <!-- Gambar Container -->
            <div class="flex-1 overflow-auto flex items-center justify-center p-4">
                <img id="modalImage" src="" alt="Full Size" class="max-h-[75vh] max-w-full object-contain">
            </div>
            
            <!-- Controls -->
            <div class="flex justify-center gap-3 p-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                <button onclick="zoomIn()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                    🔍+ Perbesar
                </button>
                <button onclick="zoomOut()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                    🔍− Perkecil
                </button>
                <button onclick="resetZoom()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                    ⟲ Reset
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentZoom = 1;
        
        function openModal(imageSrc) {
            document.getElementById('imageModal').classList.remove('hidden');
            document.getElementById('modalImage').src = imageSrc;
            currentZoom = 1;
            updateImageZoom();
        }
        
        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function zoomIn() {
            currentZoom += 0.25;
            updateImageZoom();
        }

        function zoomOut() {
            if (currentZoom > 0.25) {
                currentZoom -= 0.25;
                updateImageZoom();
            }
        }

        function resetZoom() {
            currentZoom = 1;
            updateImageZoom();
        }

        function updateImageZoom() {
            const img = document.getElementById('modalImage');
            img.style.transform = `scale(${currentZoom})`;
            img.style.transition = "transform 0.2s";
        }

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
        
        // === AUTO REFRESH CHAT ===
        const ticketUuid = "{{ $ticket->uuid }}";
        const chatContainer = document.getElementById('chat-history');
        const scrollContainer = document.getElementById('chat-container-scroll');
        let isUserScrolling = false;

        // Detect if user is scrolling up (don't auto scroll bottom if reading history)
        scrollContainer.addEventListener('scroll', () => {
            if (scrollContainer.scrollTop + scrollContainer.clientHeight < scrollContainer.scrollHeight - 50) {
                isUserScrolling = true;
            } else {
                isUserScrolling = false;
            }
        });

        // Auto Scroll Bottom on Load
        function scrollToBottom() {
            if (!isUserScrolling) {
                scrollContainer.scrollTo({ top: scrollContainer.scrollHeight, behavior: 'smooth' });
            }
        }
        setTimeout(scrollToBottom, 500); // Initial scroll

        // Poll every 10 seconds to reduce server load
        // DISABLED BY USER REQUEST (Localhost Performance)
        /*
        setInterval(() => {
            // Only poll if tab is active/visible
            if (document.hidden) return;

            fetch(`{{ route('laporan.chat.history') }}?uuid=${ticketUuid}`)
                .then(response => response.text())
                .then(html => {
                    chatContainer.innerHTML = html;
                    // Only scroll if user hasn't scrolled up
                    scrollToBottom();
                })
                .catch(err => console.error('Gagal refresh chat:', err));
        }, 10000); // 10 Detik
        */

    </script>
</body>
</html>