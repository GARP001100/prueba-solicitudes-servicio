<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ isset($title) ? $title.' | ' : '' }}{{ config('app.name') }}
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-slate-100 font-sans text-slate-900 antialiased">
    <div
        x-data="{ sidebarOpen: false }"
        class="min-h-screen"
    >
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 z-30 bg-slate-950/60 lg:hidden"
            x-on:click="sidebarOpen = false"
            style="display: none"
        ></div>

        @include('layouts.partials.sidebar')

        <div class="flex min-h-screen flex-col lg:pl-72">
            @include('layouts.partials.header', [
                'header' => isset($header) ? $header : null,
            ])

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <div class="mx-auto max-w-7xl">
                    <x-admin.alert />

                    {{ $slot }}
                </div>
            </main>

            @include('layouts.partials.footer')
        </div>

        <x-admin.loader />
    </div>

    @stack('scripts')
</body>
</html>
