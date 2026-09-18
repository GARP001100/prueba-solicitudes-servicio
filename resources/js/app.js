import './bootstrap';
import './vendor-styles';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('adminUi', {
    loading: false,
    loadingTitle: 'Procesando solicitud',
    loadingMessage: 'Estamos preparando la información.',
});

Alpine.start();

document.addEventListener('DOMContentLoaded', async () => {
    const { initializeAdminUi } = await import('./modules/admin-ui');

    initializeAdminUi();

    const dynamicCrud = document.querySelector('[data-dynamic-crud]');

    if (dynamicCrud) {
        const { initializeDynamicCrud } = await import(
            './modules/dynamic-crud'
        );

        initializeDynamicCrud(dynamicCrud);
    }

    if (document.querySelector('[data-tom-select]')) {
        const { initializeSelects } = await import('./modules/selects');

        initializeSelects();
    }

    if (
        document.querySelector('[data-datepicker]') ||
        document.querySelector('[data-datetimepicker]')
    ) {
        const { initializeDatepickers } = await import(
            './modules/datepicker'
        );

        initializeDatepickers();
    }

    const schedulingPage = document.querySelector(
        '[data-doctor-directory], ' +
        '[data-doctor-calendar], ' +
        '[data-user-appointments], ' +
        '[data-admin-appointments]'
    );

    if (schedulingPage) {
        const { initializeDoctorScheduling } = await import(
            './modules/doctor-scheduling'
        );

        initializeDoctorScheduling(schedulingPage);
    }
});
