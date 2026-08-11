<div class="w-full relative"
    x-data="{
        aktif: 0,
        timers: [],
        mengalihkan: false,
        langkah: [
            { judul: 'Memeriksa kelengkapan formulir', detail: 'Memvalidasi data pelapor dan detail masalah.' },
            { judul: 'Membaca isi keluhan', detail: 'Menelaah penjelasan yang Anda tuliskan.' },
            { judul: 'Menentukan kategori dan urgensi', detail: 'Mencocokkan laporan dengan kategori layanan yang tersedia.' },
            { judul: 'Menyimpan tiket', detail: 'Memasukkan tiket ke antrean tim IT Support.' },
        ],
        mulai() {
            this.timers.forEach(t => clearTimeout(t));
            this.timers = [];
            this.aktif = 0;
            [1200, 3200, 6500].forEach((jeda, i) => {
                this.timers.push(setTimeout(() => { this.aktif = i + 1 }, jeda));
            });
        },
        init() {
            /* Livewire mengembalikan DOM form dulu, baru menjalankan redirect. Tanpa penguncian
               ini form terlihat sekilas selama browser memuat halaman sukses. */
            Livewire.hook('commit', ({ succeed }) => {
                succeed((payload) => {
                    const efek = (payload && (payload.effects || payload.effect)) || {};
                    if (! efek.redirect) return;
                    this.timers.forEach(t => clearTimeout(t));
                    this.mengalihkan = true;
                    /* Reaktivitas Alpine baru di-flush belakangan, sedangkan form sudah tampil
                       kembali di sini. Jadi penutupnya dipasang langsung, tanpa menunggu. */
                    this.$refs.penutup.style.display = 'flex';
                });
            });
        },
    }"
    x-on:submit="mulai()"
>
    <!-- Form Area (disembunyikan saat proses berjalan) -->
    <div wire:loading.remove wire:target="create" class="w-full">
        <form wire:submit="create">
            {{ $this->form }}

            @if($errors->has('rate_limit'))
                <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300">
                    {{ $errors->first('rate_limit') }}
                </div>
            @endif

            <div class="mt-8">
                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-base font-semibold text-white shadow-sm transition-colors duration-150 hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                    Kirim Laporan
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h15m0 0-5.5-5.5M19 12l-5.5 5.5" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Layar Proses (muncul saat laporan dikirim) -->
    <div wire:loading.flex wire:target="create" class="w-full flex-col py-6">

        <!-- Judul -->
        <div class="flex items-start gap-3.5">
            <div class="mt-0.5 flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                <img src="{{ asset('img/logo-ptpn.png') }}" class="h-6 w-6 object-contain" alt="">
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Laporan sedang diproses
                </h3>
                <p class="mt-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                    Sistem sedang menelaah keluhan Anda untuk menentukan kategori dan tingkat urgensi tiket.
                </p>
            </div>
        </div>

        <!-- Daftar Tahapan -->
        <ol class="mt-7 space-y-1 border-t border-gray-100 pt-6 dark:border-gray-800">
            <template x-for="(item, i) in langkah" :key="i">
                <li class="flex items-start gap-3 rounded-lg px-2 py-2.5 transition-colors duration-300"
                    :class="i === aktif ? 'bg-gray-50 dark:bg-gray-800/60' : ''">

                    <!-- Penanda status -->
                    <span class="mt-0.5 flex h-5 w-5 flex-shrink-0 items-center justify-center">
                        <!-- Selesai -->
                        <svg x-show="i < aktif" class="h-5 w-5 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" stroke-width="1.5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.5 12 2.5 2.5 4.5-5" />
                        </svg>
                        <!-- Sedang berjalan -->
                        <svg x-show="i === aktif" class="h-4 w-4 animate-spin text-blue-600 dark:text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                            <path class="opacity-90" fill="currentColor" d="M12 2a10 10 0 0 1 10 10h-3a7 7 0 0 0-7-7V2Z" />
                        </svg>
                        <!-- Menunggu -->
                        <span x-show="i > aktif" class="h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    </span>

                    <span class="min-w-0">
                        <span class="block text-sm font-medium transition-colors duration-300"
                              :class="i > aktif ? 'text-gray-400 dark:text-gray-500' : 'text-gray-900 dark:text-gray-100'"
                              x-text="item.judul"></span>
                        <span x-show="i === aktif"
                              class="mt-0.5 block text-xs leading-relaxed text-gray-500 dark:text-gray-400"
                              x-text="item.detail"></span>
                    </span>
                </li>
            </template>
        </ol>

        <!-- Garis proses -->
        <div class="mt-6 h-1 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
            <div class="h-full w-1/3 rounded-full bg-blue-600/70 dark:bg-blue-500/70" style="animation: garis-proses 1.8s ease-in-out infinite;"></div>
        </div>

        <p class="mt-4 text-xs leading-relaxed text-gray-400 dark:text-gray-500">
            Proses ini biasanya selesai dalam beberapa detik. Mohon jangan menutup atau memuat ulang halaman.
        </p>
    </div>

    <!--
        Penutup layar saat berpindah ke halaman tiket.
        wire:ignore wajib: tanpa itu proses morph Livewire akan menimpa status tampilnya.
    -->
    <div wire:ignore
         x-ref="penutup"
         x-show="mengalihkan"
         style="display: none;"
         class="fixed inset-0 z-50 flex flex-col items-center justify-center gap-4 bg-[#F5F6F8] px-6 text-center dark:bg-gray-950">
        <svg class="h-6 w-6 animate-spin text-blue-600 dark:text-blue-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
            <path class="opacity-90" fill="currentColor" d="M12 2a10 10 0 0 1 10 10h-3a7 7 0 0 0-7-7V2Z" />
        </svg>
        <div>
            <p class="text-base font-semibold text-gray-900 dark:text-white">Laporan berhasil dikirim</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Membuka halaman tiket Anda...</p>
        </div>
    </div>

    <x-filament-actions::modals />
</div>
