import preset from './vendor/filament/support/tailwind.config.preset'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

export default {
    presets: [preset],
    darkMode: 'class', // <--- TAMBAHKAN BARIS PENTING INI
    content: [
        './app/**/*.php', // <--- Perbaiki typo (sebelumnya double slash //)
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        forms,
        typography,
    ],
}