import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';
import flatpickr from 'flatpickr';
import TomSelect from 'tom-select';

// Make Alpine available globally
window.Alpine = Alpine;
Alpine.start();

// Make ApexCharts global for dashboard charts
window.ApexCharts = ApexCharts;

// Auto-init flatpickr on date inputs
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-datepicker]').forEach(el => {
        flatpickr(el, { dateFormat: 'd/m/Y', allowInput: true });
    });

    document.querySelectorAll('[data-select]').forEach(el => {
        new TomSelect(el, { plugins: ['remove_button'] });
    });
});