<div>
    <form wire:submit="create">
        {{ $this->form }}

        @if($errors->has('rate_limit'))
            <div class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm font-bold text-center">
                {{ $errors->first('rate_limit') }}
            </div>
        @endif

        <div class="mt-6">
            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                🚀 Kirim Laporan
            </button>
        </div>
    </form>
    
    <x-filament-actions::modals />
</div>