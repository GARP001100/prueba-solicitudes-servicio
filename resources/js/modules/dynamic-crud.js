import axios from 'axios';
import Swal from 'sweetalert2';
import { Notyf } from 'notyf';
import TomSelect from 'tom-select';
import {
    ChevronLeft,
    ChevronRight,
    Database,
    KeyRound,
    PanelsTopLeft,
    Pencil,
    Plus,
    Save,
    Search,
    ShieldCheck,
    Trash2,
    UsersRound,
    X,
    createIcons,
} from 'lucide';

const notyf = new Notyf({ duration: 3000, dismissible: true, position: { x: 'right', y: 'top' } });

const escapeHtml = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const renderIcons = () => createIcons({
    icons: { ChevronLeft, ChevronRight, Database, KeyRound, PanelsTopLeft, Pencil, Plus, Save, Search, ShieldCheck, Trash2, UsersRound, X },
});

export function initializeDynamicCrud(root) {
    const resource = root.dataset.resource;
    const baseUrl = `/admin/crud/${resource}`;
    const state = { metadata: null, page: 1, search: '', editId: null, selects: [] };
    const head = root.querySelector('[data-crud-head]');
    const body = root.querySelector('[data-crud-body]');
    const summary = root.querySelector('[data-crud-summary]');
    const pagination = root.querySelector('[data-crud-pagination]');
    const search = root.querySelector('[data-crud-search]');
    const overlay = root.querySelector('[data-crud-overlay]');
    const drawer = root.querySelector('[data-crud-drawer]');
    const form = root.querySelector('[data-crud-form]');
    const fields = root.querySelector('[data-crud-fields]');
    const formTitle = root.querySelector('[data-crud-form-title]');
    const newLabel = root.querySelector('[data-crud-new-label]');

    const loader = (show, title = 'Procesando solicitud', message = 'Espera un momento.') => {
        if (!window.AdminUI) return;
        show ? window.AdminUI.showLoader(title, message) : window.AdminUI.hideLoader();
    };

    const destroySelects = () => {
        state.selects.forEach((select) => select.destroy());
        state.selects = [];
    };

    const openDrawer = () => {
        overlay.classList.remove('hidden');
        drawer.classList.remove('translate-x-full');
        document.body.classList.add('overflow-hidden');
    };

    const closeDrawer = () => {
        destroySelects();
        overlay.classList.add('hidden');
        drawer.classList.add('translate-x-full');
        document.body.classList.remove('overflow-hidden');
        form.reset();
        fields.innerHTML = '';
        state.editId = null;
    };

    const fieldHtml = (field, value = '') => {
        const required = field.required ? 'required' : '';
        const error = `<p data-field-error="${escapeHtml(field.name)}" class="mt-1 hidden text-sm text-red-600"></p>`;

        if (field.type === 'select' || field.type === 'multiselect') {
            const values = Array.isArray(value) ? value.map(String) : [String(value ?? '')];
            const options = Object.entries(field.options ?? {}).map(([key, label]) => {
                const selected = values.includes(String(key)) ? 'selected' : '';
                return `<option value="${escapeHtml(key)}" ${selected}>${escapeHtml(label)}</option>`;
            }).join('');
            const multiple = field.type === 'multiselect';
            const name = multiple ? `${field.name}[]` : field.name;

            return `<div><label class="mb-1 block text-sm font-medium text-slate-700" for="crud-${escapeHtml(field.name)}">${escapeHtml(field.label)}</label><select id="crud-${escapeHtml(field.name)}" name="${escapeHtml(name)}" data-crud-select data-multiple="${multiple}" class="w-full" ${multiple ? 'multiple' : ''} ${required}>${multiple ? '' : '<option value="">Seleccione...</option>'}${options}</select>${error}</div>`;
        }

        const hint = field.type === 'password' && state.editId
            ? '<p class="mt-1 text-xs text-slate-500">Déjala vacía para conservar la contraseña actual.</p>'
            : '';

        const control = field.type === 'textarea'
            ? `<textarea id="crud-${escapeHtml(field.name)}" name="${escapeHtml(field.name)}" rows="4" class="w-full rounded-xl border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700" ${required}>${escapeHtml(value)}</textarea>`
            : `<input id="crud-${escapeHtml(field.name)}" name="${escapeHtml(field.name)}" type="${escapeHtml(field.type)}" value="${field.type === 'password' ? '' : escapeHtml(value)}" class="w-full rounded-xl border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-700 focus:ring-blue-700" ${required}>`;

        return `<div><label class="mb-1 block text-sm font-medium text-slate-700" for="crud-${escapeHtml(field.name)}">${escapeHtml(field.label)}</label>${control}${hint}${error}</div>`;
    };

    const renderForm = (values = {}) => {
        destroySelects();
        fields.innerHTML = state.metadata.fields.map((field) => fieldHtml(field, values[field.name])).join('');
        fields.querySelectorAll('[data-crud-select]').forEach((element) => {
            const multiple = element.dataset.multiple === 'true';
            state.selects.push(new TomSelect(element, {
                create: false,
                allowEmptyOption: true,
                plugins: multiple ? ['remove_button'] : [],
            }));
        });
        renderIcons();
    };

    const renderTable = (data) => {
        const listFields = data.listFields;
        head.innerHTML = `<tr>${listFields.map((field) => `<th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">${escapeHtml(data.labels[field] ?? field)}</th>`).join('')}<th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Acciones</th></tr>`;
        body.innerHTML = data.records.length
            ? data.records.map((record) => `<tr class="hover:bg-slate-50">${listFields.map((field) => `<td class="max-w-xs whitespace-nowrap px-5 py-4 text-sm text-slate-700"><span class="block truncate">${escapeHtml(record[field])}</span></td>`).join('')}<td class="whitespace-nowrap px-5 py-4 text-right"><button type="button" data-crud-edit="${record.id}" class="mr-2 rounded-lg p-2 text-blue-700 hover:bg-blue-50" title="Editar"><i data-lucide="pencil" class="h-4 w-4"></i></button><button type="button" data-crud-delete="${record.id}" class="rounded-lg p-2 text-red-700 hover:bg-red-50" title="Eliminar"><i data-lucide="trash-2" class="h-4 w-4"></i></button></td></tr>`).join('')
            : '<tr><td colspan="99" class="px-5 py-12 text-center text-sm text-slate-500">No se encontraron registros.</td></tr>';

        const p = data.pagination;
        summary.textContent = p.total ? `Mostrando ${p.from} a ${p.to} de ${p.total}` : 'Sin registros';
        pagination.innerHTML = `<p class="text-sm text-slate-500">Página ${p.currentPage} de ${p.lastPage}</p><div class="flex gap-2"><button type="button" data-page="${p.currentPage - 1}" class="rounded-lg border border-slate-300 p-2 disabled:opacity-40" ${p.currentPage <= 1 ? 'disabled' : ''}><i data-lucide="chevron-left" class="h-4 w-4"></i></button><button type="button" data-page="${p.currentPage + 1}" class="rounded-lg border border-slate-300 p-2 disabled:opacity-40" ${p.currentPage >= p.lastPage ? 'disabled' : ''}><i data-lucide="chevron-right" class="h-4 w-4"></i></button></div>`;
        renderIcons();
    };

    const load = async (page = 1) => {
        loader(true, 'Consultando registros', 'Actualizando el listado.');
        try {
            const response = await axios.get(`${baseUrl}/data`, { params: { page, search: state.search } });
            state.metadata = response.data.data;
            state.page = state.metadata.pagination.currentPage;
            newLabel.textContent = `Nuevo ${state.metadata.singular}`;
            renderTable(state.metadata);
        } catch (error) {
            notyf.error(error.response?.data?.message ?? 'No fue posible cargar los registros.');
        } finally {
            loader(false);
        }
    };

    const showErrors = (errors = {}) => {
        Object.entries(errors).forEach(([field, messages]) => {
            const element = fields.querySelector(`[data-field-error="${CSS.escape(field)}"]`);
            if (element) {
                element.textContent = messages[0];
                element.classList.remove('hidden');
            }
        });
    };

    root.querySelector('[data-crud-new]').addEventListener('click', () => {
        state.editId = null;
        formTitle.textContent = `Nuevo ${state.metadata.singular}`;
        renderForm();
        openDrawer();
    });

    root.querySelectorAll('[data-crud-close]').forEach((button) => button.addEventListener('click', closeDrawer));
    overlay.addEventListener('click', closeDrawer);

    let timer;
    search.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            state.search = search.value.trim();
            load(1);
        }, 350);
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('[data-page]');
        if (button && !button.disabled) load(Number(button.dataset.page));
    });

    body.addEventListener('click', async (event) => {
        const edit = event.target.closest('[data-crud-edit]');
        const remove = event.target.closest('[data-crud-delete]');

        if (edit) {
            loader(true, 'Cargando registro', 'Preparando el formulario.');
            try {
                const response = await axios.get(`${baseUrl}/${edit.dataset.crudEdit}`);
                state.editId = Number(edit.dataset.crudEdit);
                formTitle.textContent = `Editar ${state.metadata.singular}`;
                renderForm(response.data.data.data);
                openDrawer();
            } catch (error) {
                notyf.error(error.response?.data?.message ?? 'No fue posible cargar el registro.');
            } finally {
                loader(false);
            }
        }

        if (remove) {
            const confirmation = await Swal.fire({
                icon: 'warning',
                title: `¿Eliminar este ${state.metadata.singular}?`,
                text: 'Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#b91c1c',
            });
            if (!confirmation.isConfirmed) return;

            loader(true, 'Eliminando registro', 'Procesando la solicitud.');
            try {
                const response = await axios.delete(`${baseUrl}/${remove.dataset.crudDelete}`);
                notyf.success(response.data.message);
                await load(state.page);
            } catch (error) {
                notyf.error(error.response?.data?.errors?.record?.[0] ?? error.response?.data?.message ?? 'No fue posible eliminar el registro.');
            } finally {
                loader(false);
            }
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        fields.querySelectorAll('[data-field-error]').forEach((item) => item.classList.add('hidden'));

        const formData = new FormData(form);
        const payload = {};
        for (const [name, value] of formData.entries()) {
            if (name.endsWith('[]')) {
                const cleanName = name.slice(0, -2);
                payload[cleanName] ??= [];
                payload[cleanName].push(value);
            } else {
                payload[name] = value;
            }
        }

        loader(true, state.editId ? 'Actualizando registro' : 'Creando registro', 'Validando la información.');
        try {
            const response = state.editId
                ? await axios.patch(`${baseUrl}/${state.editId}`, payload)
                : await axios.post(baseUrl, payload);
            notyf.success(response.data.message);
            closeDrawer();
            await load(state.page);
        } catch (error) {
            if (error.response?.status === 422) showErrors(error.response.data.errors);
            notyf.error(error.response?.data?.message ?? 'Revisa la información del formulario.');
        } finally {
            loader(false);
        }
    });

    renderIcons();
    load();
}
