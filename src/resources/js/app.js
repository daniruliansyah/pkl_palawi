import './bootstrap';

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    flatpickr(".datepicker", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        allowInput: true,
    });
});
