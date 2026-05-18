import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './database/seeders/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'brand-red': '#ed1e28',
                'brand-red-dark': '#c91a23',
                'brand-red-light': '#fff1f2',
            },
            boxShadow: {
                'brand-glow': '0 0 20px rgba(237, 30, 40, 0.15)',
                'premium': '0 10px 30px -10px rgba(15, 23, 42, 0.04), 0 4px 10px -5px rgba(15, 23, 42, 0.02)',
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
            }
        },
    },

    plugins: [forms, typography],
};
