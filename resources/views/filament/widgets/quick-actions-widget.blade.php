@php
    $data = $this->getUserData();
@endphp

<x-filament-widgets::widget>
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-700 via-teal-800 to-emerald-900 rounded-2xl shadow-md border border-teal-600/30 text-white p-6 md:p-8">
        <!-- Background Watermark Pattern -->
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none select-none text-9xl">
            🏥
        </div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $data['roleBadgeColor'] }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                        {{ $data['roleLabel'] }}
                    </span>
                    <span class="text-xs text-teal-200">
                        Station: <strong>{{ $data['department'] }}</strong>
                    </span>
                </div>
                <h2 class="text-xl md:text-2xl font-black tracking-tight text-white">
                    Roxas Memorial Provincial Hospital • Consignment Operations Center
                </h2>
                <p class="text-xs md:text-sm text-teal-100/90 mt-1 max-w-xl">
                    Real-time FEFO batch management, perpetual stockcard tracking, and multi-department hospital consignment oversight.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <a href="{{ route('filament.admin.resources.deliveries.create') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-teal-950 bg-teal-200 hover:bg-white rounded-xl shadow-sm transition active:scale-95">
                    <span>📥</span> Receive Delivery (DR)
                </a>

                <a href="{{ route('filament.admin.resources.stock-requests.create') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-white bg-white/20 hover:bg-white/30 backdrop-blur-md rounded-xl transition border border-white/20 active:scale-95">
                    <span>📤</span> New Requisition (RIS)
                </a>

                <a href="{{ route('filament.admin.pages.inventory-reports') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-white bg-white/20 hover:bg-white/30 backdrop-blur-md rounded-xl transition border border-white/20 active:scale-95">
                    <span>📊</span> Reports & Ledger
                </a>

                <a href="{{ route('purchase-request.critical') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-amber-950 bg-amber-300 hover:bg-white rounded-xl shadow-sm transition active:scale-95">
                    <span>📑</span> Critical PR (App. 47)
                </a>

                <a href="{{ route('portal') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-emerald-950 bg-emerald-300 hover:bg-white rounded-xl shadow-sm transition active:scale-95">
                    <span>🛒</span> Ward Portal
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
