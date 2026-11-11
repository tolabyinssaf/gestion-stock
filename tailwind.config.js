import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#014392',
                    light: '#0161B9',
                    dark: '#013070',
                },
                accent: {
                    DEFAULT: '#FE7A04',
                    light: '#FF9A3C',
                },
                surface: {
                    DEFAULT: '#F4F7FB',
                    white: '#FFFFFF',
                },
                content: {
                    DEFAULT: '#1E293B',
                    secondary: '#64748B',
                },
                border: {
                    DEFAULT: '#E2E8F0',
                },
            },
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
