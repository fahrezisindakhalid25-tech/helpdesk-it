<div class="flex justify-center items-start min-h-dvh flex-col w-6/12 m-auto gap-3 py-24">
    <div class="flex justify-between gap-3 items-center w-full">
        <div class="flex flex-row items-center gap-3">
            <img src="{{ asset('img/logo-ptpn.png') }}" alt="logo" class='h-18'>
            <div class="flex flex-col">
                <h1 class='font-bold text-xl dark:text-white'>IT Helpdesk</h1>
                <span class='text-xs dark:text-white'>PTPN IV Regional II</span>
            </div>
        </div>
        <div class="flex">
            <x-filament::button wire:click="$js.toggleTheme" label="toggle-theme" class='border-2 fi-size-lg'>
                <!-- Sun Icon -->
                <svg class="hidden dark:block w-5 h-5 text-yellow-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <!-- Moon Icon -->
                <svg class="block dark:hidden w-5 h-5 text-gray-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                    </path>
            </x-filament::button>
        </div>
    </div>
    <div class="flex">
        <span class='font-semibold text-md dark:text-white'>Ada kendala teknis? silahkan isi form di bawah ini dan kami akan segera
            membantu Anda.</span>
    </div>
    <form wire:submit="create" class='w-full'>
        <x-filament::section>
            {{ $this->form }}
            <x-filament::button type='submit' class='mt-6 w-full text-center'>
                Kirim Laporan
            </x-filament::button>
        </x-filament::section>
    </form>
</div>

@script
    <script>
        const theme = localStorage.getItem('user-theme') ?? 'system';
        if (
            theme === 'dark' ||
            (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        this.$js.toggleTheme = () => {
            console.log('toggleTheme', 'triggered')
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
@endscript
