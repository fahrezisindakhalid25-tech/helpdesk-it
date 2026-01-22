<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket #{{ $ticket->no_tiket }} - Helpdesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex flex-col">

    <div class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 sticky top-0 z-10">
        <div class="max-w-3xl mx-auto px-4 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">Tiket #{{ $ticket->no_tiket }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $ticket->deskripsi_umum_masalah }}</p>
            </div>
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

    <div class="flex-1 overflow-y-auto p-4">
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

            @forelse($ticket->comments as $comment)
                <div class="flex {{ $comment->user_id ? 'justify-start' : 'justify-end' }}">
                    
                    @if($comment->user_id)
                        <div class="flex gap-3 max-w-[80%]">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                A
                            </div>
                            <div>
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 shadow-sm">
                                    <p class="text-sm font-bold text-blue-600 mb-1">Admin Support</p>
                                    <p class="text-gray-800 dark:text-gray-200 text-sm whitespace-pre-wrap">{{ $comment->content }}</p>
                                </div>
                                <span class="text-xs text-gray-400 ml-1">{{ $comment->created_at->format('H:i') }}</span>
                            </div>
                        </div>

                    @else
                        <div class="flex gap-3 max-w-[80%] flex-row-reverse">
                            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                U
                            </div>
                            <div>
                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 shadow-sm">
                                    <p class="text-sm font-bold text-green-700 dark:text-green-400 mb-1">Anda</p>
                                    <p class="text-gray-800 dark:text-gray-200 text-sm whitespace-pre-wrap">{{ $comment->content }}</p>
                                </div>
                                <span class="text-xs text-gray-400 mr-1 text-right block">{{ $comment->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    @endif

                </div>
            @empty
                <p class="text-center text-gray-400 text-sm italic">Belum ada percakapan.</p>
            @endforelse

        </div>
    </div>

<div class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 p-4 sticky bottom-0">
        <div class="max-w-3xl mx-auto">

            {{-- 1. TAMPILKAN ERROR VALIDASI (Jika ada yang mencoba hack lewat inspect element) --}}
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Gagal!</strong>
                    <span class="block sm:inline">{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- 2. LOGIKA TAMPILAN FORM --}}
            
            {{-- KONDISI A: TIKET EXPIRED / CLOSED --}}
            @if($isExpired)
                <div class="text-center p-4 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 rounded-lg flex flex-col items-center">
                    <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span class="font-bold">Tiket Ditutup</span>
                    <span class="text-xs">Masa berlaku tiket (5 Hari) telah habis atau masalah dinyatakan selesai permanen.</span>
                </div>

            {{-- KONDISI B: ADMIN BELUM JAWAB (User Harus Menunggu) --}}
            @elseif(!$adminSudahJawab)
                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 rounded-lg flex flex-col items-center animate-pulse">
                    <svg class="w-8 h-8 mb-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold">Menunggu Respon Admin</span>
                    <span class="text-xs">Anda dapat membalas pesan setelah Admin merespon laporan ini.</span>
                </div>

            {{-- KONDISI C: NORMAL (Bisa Chat & Re-Open Solved Ticket) --}}
            @else
                <form action="{{ route('laporan.reply', $ticket->uuid) }}" method="POST" class="flex gap-2">
                    @csrf
                    <textarea name="isi_pesan" rows="1" required placeholder="Tulis balasan Anda..." class="flex-1 appearance-none border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center gap-2">
                        <span>Kirim</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
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
    </script>
</body>
</html>