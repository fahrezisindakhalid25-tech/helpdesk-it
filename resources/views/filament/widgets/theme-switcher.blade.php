<div class="flex gap-2 items-center">
    <span class="text-sm text-gray-600 dark:text-gray-400">Theme:</span>
    <button 
        wire:click="switchTheme('light')"
        @class([
            'px-3 py-1 rounded-md text-sm font-medium transition',
            'bg-yellow-100 text-yellow-800' => $this->getCurrentTheme() === 'light',
            'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400' => $this->getCurrentTheme() !== 'light',
        ])
        title="Light Mode"
    >
        ☀️ Light
    </button>
    
    <button 
        wire:click="switchTheme('dark')"
        @class([
            'px-3 py-1 rounded-md text-sm font-medium transition',
            'bg-gray-900 text-white' => $this->getCurrentTheme() === 'dark',
            'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400' => $this->getCurrentTheme() !== 'dark',
        ])
        title="Dark Mode"
    >
        🌙 Dark
    </button>
    
    <button 
        wire:click="switchTheme('system')"
        @class([
            'px-3 py-1 rounded-md text-sm font-medium transition',
            'bg-blue-100 text-blue-800' => $this->getCurrentTheme() === 'system',
            'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400' => $this->getCurrentTheme() !== 'system',
        ])
        title="System Theme"
    >
        🖥️ System
    </button>
</div>
