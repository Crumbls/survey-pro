import preset from './vendor/filament/support/tailwind.config.preset'

/** @type {import('tailwindcss').Config} */
module.exports = {
    presets: [preset],
    content: [
        "./resources/**/*.blade.php",
        "./resources/js/**/!(node_modules)/**/*.js", // Exclude node_modules
        './app/Filament/**/*.php',
        './vendor/filament/**/*.blade.php',
        './vendor/padmission/**/*.blade.php',
        './packages/issues/resources/views/**/*.blade.php'

    ],
    theme: {
        extend: {
            spacing: { '18': '4.5rem' },
            colors: {
                teal: {
                    50: '#f0fdfa',
                    600: '#0d9488',
                    700: '#0f766e',
                },
                custom: {
                    50:  '#f0f9ff',  // Lightest shade
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',  // Base color
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',  // Darkest shade
                },

                primary: {
                    50:  '#f0f9ff',  // Lightest shade
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',  // Base color
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',  // Darkest shade
                },
                // Secondary/complementary color
                secondary: {
                    50:  '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',
                    500: '#f97316',  // Base color
                    600: '#ea580c',
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                },
                // Accent/inverse color
                accent: {
                    50:  '#fdf2f8',
                    100: '#fce7f3',
                    200: '#fbcfe8',
                    300: '#f9a8d4',
                    400: '#f472b6',
                    500: '#ec4899',  // Base color
                    600: '#db2777',
                    700: '#be185d',
                    800: '#9d174d',
                    900: '#831843',
                }
            }
        },
    },
    plugins: [],
    safelist: [
        {
            pattern: /^sd-/ // This will catch all classes starting with sd-
        },
        {
            pattern: /^spg-/ // This will catch all classes starting with sd-
        },
        {
            pattern: /^svc-/ // This will catch all classes starting with sd-
        }
    ]
}
