<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-900 antialiased">
    <div class="grid min-h-screen bg-white lg:grid-cols-2">
        <section class="relative hidden overflow-hidden bg-blue-950 lg:flex lg:flex-col lg:justify-between lg:p-12">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-700/45 via-blue-950 to-slate-950"></div>

            <div class="absolute -left-32 top-20 h-80 w-80 rounded-full bg-blue-400/20 blur-3xl"></div>

            <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-amber-400/10 blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-12 w-12 items-center justify-center overflow-hidden rounded-xl bg-white font-black text-blue-950 shadow-xl">
                        JP
                        <span class="absolute bottom-0 h-1 w-full bg-amber-400"></span>
                    </div>

                    <div>
                        <p class="font-bold text-white">
                            {{ config('app.name') }}
                        </p>

                        <p class="text-sm text-blue-200">
                            Plataforma administrativa
                        </p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 max-w-lg">
                <div class="mb-6 h-1 w-16 rounded-full bg-amber-400"></div>

                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-200">
                    Gestión institucional
                </p>

                <h1 class="mt-4 text-4xl font-bold leading-tight text-white xl:text-5xl">
                    Información disponible, segura y oportuna.
                </h1>

                <p class="mt-5 max-w-md text-base leading-7 text-blue-100/80">
                    Administra procesos, usuarios, incidentes y servicios desde una plataforma centralizada.
                </p>
            </div>

            <p class="relative z-10 text-xs text-blue-300/70">
                &copy; {{ now()->year }} {{ config('app.name') }}
            </p>
        </section>

        <main class="flex min-h-screen items-center justify-center bg-slate-50 px-5 py-10 sm:px-10">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <div class="flex items-center justify-center gap-3">
                        <div class="relative flex h-11 w-11 items-center justify-center overflow-hidden rounded-xl bg-blue-950 font-bold text-white">
                            JP
                            <span class="absolute bottom-0 h-1 w-full bg-amber-400"></span>
                        </div>

                        <p class="font-bold text-slate-900">
                            {{ config('app.name') }}
                        </p>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-300/40 sm:p-8">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs text-slate-400 lg:hidden">
                    &copy; {{ now()->year }} {{ config('app.name') }}
                </p>
            </div>
        </main>
    </div>

    <x-admin.loader />
</body>
</html>
