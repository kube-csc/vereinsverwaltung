const defaultTheme = require('tailwindcss-v3/defaultTheme');
const colors = require('tailwindcss-v3/colors');

module.exports = {
    // Wie unter Tailwind 2: kein automatischer Dark Mode über die Systemeinstellung.
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // Backend-Design in Schwarz- und Grautönen: Akzentfarben und das bläuliche Grau
            // werden auf neutrale Töne gelegt. Rot, Grün und Gelb bleiben für Status-Meldungen.
            colors: {
                gray: colors.neutral,
                blue: colors.neutral,
                indigo: colors.neutral,
                purple: colors.neutral,
            },
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [require('tailwind-forms-v3'), require('tailwind-typography-v3')],
};