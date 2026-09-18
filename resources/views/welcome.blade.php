<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <main class="relative isolate min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-950 via-blue-900 to-slate-950"></div>
        <div class="absolute -left-24 top-20 h-80 w-80 rounded-full bg-blue-400/20 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-amber-400/10 blur-3xl"></div>

        <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-8 lg:px-8">
            <header class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <x-admin.brand-mark size="lg" class="bg-white text-blue-950" />
                    <div>
                        <p class="font-bold text-white">{{ config('app.name') }}</p>
                        <p class="text-sm text-blue-200">Plataforma administrativa</p>
                    </div>
                </div>

                <nav class="flex items-center gap-3" aria-label="Acceso">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-blue-950 shadow-lg transition hover:bg-blue-50">
                            Ir al dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">
                            Iniciar sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-xl bg-amber-400 px-4 py-2.5 text-sm font-semibold text-blue-950 shadow-lg transition hover:bg-amber-300">
                                Registrarse
                            </a>
                        @endif
                    @endauth
                </nav>
            </header>

            <section class="grid flex-1 items-center gap-12 py-16 lg:grid-cols-2">
                <div class="max-w-2xl">
                    <div class="mb-6 h-1 w-20 rounded-full bg-amber-400"></div>
                    <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-200">Gestión institucional</p>
                    <h1 class="mt-5 text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">
                        Información clara para decisiones oportunas.
                    </h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-blue-100/80">
                        Un punto de partida reutilizable para administrar procesos, usuarios, incidentes, reportes y servicios.
                    </p>

                    <div class="mt-9 flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-400 px-5 py-3 font-semibold text-blue-950 shadow-xl transition hover:bg-amber-300">
                                <i data-lucide="layout-dashboard" class="h-5 w-5"></i>
                                Abrir dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 font-semibold text-blue-950 shadow-xl transition hover:bg-blue-50">
                                <i data-lucide="log-in" class="h-5 w-5"></i>
                                Acceder a la plataforma
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ([
                        ['icon' => 'shield-check', 'title' => 'Acceso seguro', 'text' => 'Autenticación, sesiones y protección de rutas.'],
                        ['icon' => 'panels-top-left', 'title' => 'Interfaz modular', 'text' => 'Componentes Blade reutilizables y responsive.'],
                        ['icon' => 'database', 'title' => 'Datos integrados', 'text' => 'Migraciones, Eloquent y soporte para APIs.'],
                        ['icon' => 'chart-no-axes-combined', 'title' => 'Indicadores', 'text' => 'Base preparada para tablas, gráficas y calendarios.'],
                    ] as $feature)
                        <article class="rounded-2xl border border-white/10 bg-white/10 p-5 text-white shadow-xl backdrop-blur">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-400 text-blue-950">
                                <i data-lucide="{{ $feature['icon'] }}" class="h-5 w-5"></i>
                            </div>
                            <h2 class="mt-4 font-semibold">{{ $feature['title'] }}</h2>
                            <p class="mt-2 text-sm leading-6 text-blue-100/75">{{ $feature['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <footer class="border-t border-white/10 pt-5 text-xs text-blue-200/70">
                &copy; {{ now()->year }} {{ config('app.name') }}.
            </footer>
        </div>
    </main>
</body>
</html>
