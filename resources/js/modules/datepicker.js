import flatpickr from 'flatpickr';
import { Spanish } from 'flatpickr/dist/l10n/es.js';

export function initializeDatepickers() {
    document.querySelectorAll('[data-datepicker]').forEach((element) => {
        flatpickr(element, {
            locale: Spanish,
            dateFormat: 'Y-m-d',
            allowInput: true,
        });
    });

    document.querySelectorAll('[data-datetimepicker]').forEach((element) => {
        flatpickr(element, {
            locale: Spanish,
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            allowInput: true,
        });
    });
}