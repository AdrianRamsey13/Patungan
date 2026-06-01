import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                coral:  { DEFAULT: '#FF6B4A', 2: '#FF8160', soft: '#FFE7DF' },
                mint:   { DEFAULT: '#12B886', ink: '#0B8A65', soft: '#DDF6EC' },
                amber:  { DEFAULT: '#F59E0B', ink: '#C77C05', soft: '#FCEFD2' },
                grape:  { DEFAULT: '#7B61FF', soft: '#ECE7FF' },
                sky:    { DEFAULT: '#2BA8F4', soft: '#DDF0FD' },
                sun:    '#FFD23F',
                ink:    { DEFAULT: '#241D16', soft: '#6B6157' },
                muted:  '#A39A8F',
                line:   { DEFAULT: '#F1E8DC', 2: '#EADFCF' },
                cream:  '#FFF7EF',
            },
            borderRadius: {
                xl:   '28px',
                lg:   '22px',
                md:   '16px',
                sm:   '11px',
                pill: '9999px',
            },
            fontFamily: {
                display: ['"Baloo 2"', 'cursive'],
                sans:    ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            spacing: {
                4.5: '18px',
            },
            boxShadow: {
                sm:   '0 1px 2px rgba(36,29,22,.05), 0 4px 12px rgba(36,29,22,.05)',
                card: '0 2px 5px rgba(36,29,22,.04), 0 14px 30px rgba(36,29,22,.07)',
                pop:  '0 10px 24px rgba(36,29,22,.10), 0 30px 60px rgba(36,29,22,.16)',
            },
        },
    },

    plugins: [forms],
};
