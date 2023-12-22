import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        hmr: {
            host: 'localhost',
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js',
            'resources/js/shift_view_calender.js',
            'resources/js/shift_planning_calendar.js',
            'resources/js/work_confirm_calender.js',
            'resources/js/work_hope_calender.js',
            'resources/js/calendar.js',
        ],
            refresh: true,
        }),
    ],
});
