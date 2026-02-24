import './bootstrap';

import Alpine from 'alpinejs';
import $ from 'jquery';
import 'select2/dist/css/select2.css';

window.Alpine = Alpine;
window.$ = $;
window.jQuery = $;

Alpine.start();

async function initSelect2() {
    const select2 = await import('select2');
    const initializer = select2?.default || select2;
    if (typeof initializer === 'function') {
        initializer(window, $);
    }

    const selects = document.querySelectorAll('[data-enhanced-select]');
    selects.forEach((select) => {
        const $select = $(select);
        if ($select.hasClass('select2-hidden-accessible')) {
            return;
        }
        $select.select2({
            width: '100%',
            placeholder: 'Seleccione...',
            allowClear: true,
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initSelect2();
});
