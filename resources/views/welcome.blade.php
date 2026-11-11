<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="refresh" content="0; url={{ url('/admin/login') }}">
        <title>RMPH HECS • Roxas Memorial Provincial Hospital</title>
        <link rel="stylesheet" href="{{ asset('css/filament/hospital-theme.css') }}">
    </head>
    <body class="bg-slate-50 text-slate-900 flex items-center justify-center min-h-screen p-6">
        <div class="max-w-md w-full p-8 rounded-xl bg-white border border-slate-200 text-center space-y-4 shadow-xs">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-teal-50 text-teal-700 border border-teal-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">
                Roxas Memorial Provincial Hospital
            </h1>
            <p class="text-xs text-slate-500">
                Redirecting to Hospital Enterprise Consignment System...
            </p>
            <div class="pt-2">
                <a href="{{ url('/admin/login') }}" class="min-h-[44px] inline-flex items-center justify-center px-4 py-2 rounded-lg bg-teal-700 text-white text-xs font-semibold hover:bg-teal-800 transition">
                    Access System Portal &rarr;
                </a>
            </div>
        </div>
    </body>
</html>
