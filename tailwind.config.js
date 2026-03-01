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
            fontFamily: {
                sans: ['Noto Sans Thai', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#E8F5E9',
                    100: '#C8E6C9',
                    200: '#A5D6A7',
                    300: '#81C784',
                    400: '#66BB6A',
                    500: '#43A047',
                    600: '#2E7D32',
                    700: '#1B5E20',
                    800: '#145218',
                    900: '#0D3B11',
                    950: '#082409',
                },
                gold: {
                    400: '#D4B96A',
                    500: '#C9A84C',
                    600: '#B8942F',
                },
                sidebar: {
                    DEFAULT: '#0C160E',
                    light: '#132815',
                    hover: '#1B3A1E',
                },
            },
        },
    },

    plugins: [forms],
};
