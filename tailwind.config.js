import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
// const colors = require('tailwindcss/colors');
import colors from 'tailwindcss/colors.js';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*Table.php',
        './vendor/power-components/livewire-powergrid/resources/views/**/*.php',
        './vendor/power-components/livewire-powergrid/src/Themes/Tailwind.php'
    ],
    darkMode: 'class',
    presets: [
        require("./vendor/power-components/livewire-powergrid/tailwind.config.js"),
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                "pg-primary": colors.gray,
            },
        },
    },

    plugins: [forms],
};
