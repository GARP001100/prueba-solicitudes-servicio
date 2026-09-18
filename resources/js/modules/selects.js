import TomSelect from 'tom-select';

export function initializeSelects() {
    document.querySelectorAll('[data-tom-select]').forEach((element) => {
        if (element.tomselect) {
            return;
        }

        new TomSelect(element, {
            create: false,
            allowEmptyOption: true,
            maxOptions: 50,
        });
    });
}