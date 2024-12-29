import preset from './vendor/filament/support/tailwind.config.preset'

/** @type {import('tailwindcss').Config} */
module.exports = {
    presets: [preset],
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',

    ],
    theme: {
        extend: {
            spacing: { '18': '4.5rem' },
            colors: {
                teal: {
                    50: '#f0fdfa',
                    600: '#0d9488',
                    700: '#0f766e',
                }
            }
        },
    },
    plugins: [],
}
