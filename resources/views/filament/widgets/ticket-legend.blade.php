<x-filament::widget>
    <x-filament::section>
        <div class="flex flex-wrap gap-4 items-center text-sm">
            <span class="font-bold text-gray-700 dark:text-gray-200">Keterangan Warna No Tiket:</span>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center min-w-[3rem] px-2 py-0.5 rounded-full text-xs font-bold" 
                      style="background-color: #fefce8; color: #a16207; border: 1px solid rgba(29, 78, 216, 0.1);">
                    Kuning 
                </span>
                <span class="text-gray-600 dark:text-gray-400">Tiket Baru/Pesan baru (Open)</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center min-w-[3rem] px-2 py-0.5 rounded-full text-xs font-bold" 
                      style="background-color: #eff6ff; color: #1d4ed8; border: 1px solid rgba(161, 98, 7, 0.2);">
                    Biru 
                </span>
                <span class="text-gray-600 dark:text-gray-400">Sudah dibalas (Replied)</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center min-w-[3rem] px-2 py-0.5 rounded-full text-xs font-bold" 
                      style="background-color: #f0fdf4; color: #15803d; border: 1px solid rgba(21, 128, 61, 0.2);">
                    Hijau
                </span>
                <span class="text-gray-600 dark:text-gray-400">Selesai (Solved)</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center min-w-[3rem] px-2 py-0.5 rounded-full text-xs font-bold" 
                      style="background-color: #fef2f2; color: #b91c1c; border: 1px solid rgba(185, 28, 28, 0.1);">
                    Merah
                </span>
                <span class="text-gray-600 dark:text-gray-400">Ditutup (Closed)</span>
            </div>
        </div>
    </x-filament::section>
</x-filament::widget>
