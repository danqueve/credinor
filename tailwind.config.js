/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./views/**/*.php",
        "./src/**/*.php",
        "./public/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50:  '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                },
                sidebar: {
                    bg:     '#0f172a',
                    hover:  '#1e293b',
                    border: '#1e293b',
                    text:   '#94a3b8',
                    muted:  '#475569',
                },
                success: '#22c55e',
                warning: '#f59e0b',
                danger:  '#ef4444',
            },
            fontFamily: {
                sans: ['-apple-system', 'BlinkMacSystemFont', 'Inter', 'Segoe UI', 'sans-serif'],
            },
        },
    },
    plugins: [],
};
