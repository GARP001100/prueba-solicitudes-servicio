import {
    Activity,
    ArrowRight,
    BadgeCheck,
    Bell,
    CalendarCheck,
    CalendarCheck2,
    CalendarClock,
    CalendarDays,
    CalendarPlus,
    ChartNoAxesCombined,
    ChevronDown,
    CircleAlert,
    CircleCheck,
    CircleUserRound,
    CircleX,
    ClipboardList,
    Component,
    Crown,
    Database,
    Eye,
    EyeOff,
    Inbox,
    LayoutDashboard,
    ListRestart,
    LoaderCircle,
    LockKeyhole,
    LogIn,
    LogOut,
    Mail,
    Menu,
    PanelsTopLeft,
    Plus,
    Save,
    Search,
    ServerCog,
    Settings,
    Shield,
    ShieldCheck,
    Stethoscope,
    TriangleAlert,
    UserPlus,
    UserRound,
    UsersRound,
    X,
    createIcons,
} from 'lucide';

function renderIcons() {
    createIcons({
        icons: {
            Activity,
            ArrowRight,
            BadgeCheck,
            Bell,
            CalendarCheck,
            CalendarCheck2,
            CalendarClock,
            CalendarDays,
            CalendarPlus,
            ChartNoAxesCombined,
            ChevronDown,
            CircleAlert,
            CircleCheck,
            CircleUserRound,
            CircleX,
            ClipboardList,
            Component,
            Crown,
            Database,
            Eye,
            EyeOff,
            Inbox,
            LayoutDashboard,
            ListRestart,
            LoaderCircle,
            LockKeyhole,
            LogIn,
            LogOut,
            Mail,
            Menu,
            PanelsTopLeft,
            Plus,
            Save,
            Search,
            ServerCog,
            Settings,
            Shield,
            ShieldCheck,
            Stethoscope,
            TriangleAlert,
            UserPlus,
            UserRound,
            UsersRound,
            X,
        },
    });
}

export function initializeAdminUi() {
    renderIcons();

    window.AdminUI = {
        showLoader(
            title = 'Procesando solicitud',
            message = 'Estamos preparando la información.',
        ) {
            const store = window.Alpine.store('adminUi');

            store.loadingTitle = title;
            store.loadingMessage = message;
            store.loading = true;
        },

        hideLoader() {
            window.Alpine.store('adminUi').loading = false;
        },

        refreshIcons() {
            renderIcons();
        },
    };

    document.querySelectorAll('[data-loading-form]').forEach((form) => {
        form.addEventListener('submit', () => {
            const title =
                form.dataset.loadingTitle ?? 'Procesando solicitud';

            const message =
                form.dataset.loadingMessage ??
                'Estamos validando la información suministrada.';

            window.AdminUI.showLoader(title, message);
        });
    });

    window.addEventListener('pageshow', () => {
        window.AdminUI.hideLoader();
    });
}
