import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Enums/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#fbf3f2',
                    100: '#f6e2df',
                    200: '#ecc2bd',
                    500: '#b8352c',
                    600: '#a3261e',
                    700: '#8c1d18',
                    800: '#721813',
                    900: '#5a130f',
                },
            },
        },
    },

    plugins: [forms],
};
