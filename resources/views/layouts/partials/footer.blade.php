<footer class="border-t border-slate-200 bg-white px-4 py-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-1 text-center text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:text-left">
        <p>
            &copy; {{ now()->year }} {{ config('app.name') }}.
        </p>

        <p>
            Laravel {{ app()->version() }}
            <span class="mx-1" aria-hidden="true">&middot;</span>
            PHP {{ PHP_VERSION }}
        </p>
    </div>
</footer>
