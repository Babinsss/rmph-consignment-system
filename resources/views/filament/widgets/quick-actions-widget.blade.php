@php
    $data = $this->getUserData();
@endphp

<x-filament-widgets::widget>
    <div class="relative overflow-hidden bg-slate-900 rounded-2xl shadow-sm border border-slate-800 text-white p-6 sm:p-8">
        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="max-w-2xl">
                <div class="flex flex-wrap items-center gap-2.5 mb-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $data['roleBadgeColor'] }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ $data['roleLabel'] }}
                    </span>
                    <span class="text-xs text-slate-300 font-medium">
                        Hospital Station: <strong class="text-white">{{ $data['department'] }}</strong>
                    </span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white leading-tight">
                    Roxas Memorial Provincial Hospital &bull; Consignment Operations
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                    Perpetual FEFO stock allocation, multi-department oversight, and clinical audit reporting.
                </p>
            </div>

            <!-- Responsive Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <a href="{{ route('filament.admin.resources.deliveries.create') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-teal-950 bg-teal-300 hover:bg-white rounded-xl shadow-sm transition-all duration-150 min-h-[40px] focus:outline-none focus:ring-2 focus:ring-teal-400">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Receive Delivery (DR)</span>
                </a>

                <a href="{{ route('filament.admin.resources.stock-requests.create') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-all duration-150 min-h-[40px] focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>New Requisition (RIS)</span>
                </a>

                <a href="{{ route('filament.admin.pages.inventory-reports') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl transition-all duration-150 min-h-[40px] focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Reports & Ledger</span>
                </a>

                <a href="{{ route('purchase-request.critical') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-amber-950 bg-amber-300 hover:bg-white rounded-xl shadow-sm transition-all duration-150 min-h-[40px] focus:outline-none focus:ring-2 focus:ring-amber-400">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Critical PR (App. 47)</span>
                </a>

                <a href="{{ route('portal') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-emerald-950 bg-emerald-300 hover:bg-white rounded-xl shadow-sm transition-all duration-150 min-h-[40px] focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>Ward Portal</span>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
