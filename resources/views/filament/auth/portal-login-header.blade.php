<div class="mb-5 text-center">
    {{-- Fast Portal Switcher Tab at Top --}}
    <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 border border-slate-200/80 mb-4 shadow-2xs">
        <a href="{{ url('/admin/login') }}"
           title="Switch to Central Operations"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-slate-600 hover:text-teal-700 text-xs font-semibold transition">
            <x-filament::icon icon="heroicon-m-building-office-2" class="w-3.5 h-3.5 text-slate-400" />
            <span>Central Operations</span>
        </a>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white text-teal-800 font-bold text-xs shadow-2xs border border-slate-200/60">
            <x-filament::icon icon="heroicon-m-clipboard-document-list" class="w-3.5 h-3.5 text-teal-700" />
            <span>Ward Portal</span>
        </span>
    </div>

    {{-- Hospital Emblem & Identity --}}
    <div class="flex items-center justify-center gap-3 mb-2">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-teal-50 text-teal-700 border border-teal-200/80 shadow-2xs shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
        </div>
        <div class="text-left">
            <h1 class="text-lg sm:text-xl font-extrabold tracking-tight text-slate-900 leading-tight">
                Roxas Memorial Provincial Hospital
            </h1>
            <p class="text-xs text-slate-500 font-medium">
                Hospital Enterprise Consignment System (HECS)
            </p>
        </div>
    </div>

    {{-- Context Banner --}}
    <div class="mt-3 py-2 px-3 rounded-lg bg-teal-50/70 border border-teal-100 text-teal-900 text-xs flex items-center justify-between text-left">
        <div class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-m-shield-check" class="w-4 h-4 text-teal-700 shrink-0" />
            <span class="font-medium text-xs">
                Ward Requisition Portal for Clinical Care Units & Stations
            </span>
        </div>
        <span class="text-[10px] uppercase font-bold tracking-wider px-1.5 py-0.5 rounded bg-teal-200/70 text-teal-900 font-mono shrink-0">
            Wards
        </span>
    </div>

    {{-- Form Title --}}
    <div class="mt-4 text-left border-b border-slate-100 pb-2">
        <h2 class="text-sm font-bold text-slate-800">
            Sign in to Ward Portal
        </h2>
        <p class="text-xs text-slate-500">
            Authorized units: CSR, HDU, Gugma Dialysis, Main Pharmacy, OPD Pharmacy & Yakap
        </p>
    </div>
</div>
