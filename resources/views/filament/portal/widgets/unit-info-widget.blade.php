@php
    $data = $this->getUnitData();
@endphp

<x-filament-widgets::widget>
    <div class="space-y-6">
        {{-- 1. Unit Context & Schedule Banner --}}
        <div class="p-6 rounded-xl bg-white border border-slate-200/60 shadow-[0_20px_40px_-10px_rgba(0,0,0,0.03)] flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold uppercase tracking-wider bg-teal-50 text-teal-800 border border-teal-200/60 font-mono">
                        {{ $data['unitCode'] }}
                    </span>
                    <span class="text-xs text-slate-500 font-medium">
                        {{ $data['currentTime'] }}
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                    {{ $data['unitName'] }}
                </h1>
                <p class="text-xs text-slate-500">
                    Signed in as <strong class="text-slate-800 font-semibold">{{ $data['userName'] }}</strong> • Central Storage Depot Requisitions
                </p>
            </div>

            {{-- Schedule Pill --}}
            <div>
                @if ($data['isWindowOpen'])
                    <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-semibold">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                        </span>
                        <span>Schedule: <strong>OPEN</strong> ({{ $data['scheduleSummary'] }})</span>
                    </div>
                @else
                    <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-800 text-xs font-semibold">
                        <x-filament::icon icon="heroicon-m-clock" class="w-4 h-4 text-amber-600" />
                        <span>Schedule: <strong>CLOSED</strong> • STAT Emergency Requisitions 24/7</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. The Two Prominent Actions: New Request & Full-Width STAT Card --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- A. Regular / New Request Card --}}
            <a href="{{ url('/portal/requests/create') }}"
               class="min-h-[48px] p-6 rounded-xl bg-white border border-slate-200/60 shadow-[0_20px_40px_-10px_rgba(0,0,0,0.03)] hover:border-teal-600/40 hover:shadow-md transition-all group flex items-center justify-between">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-teal-700">
                        <x-filament::icon icon="heroicon-m-plus-circle" class="w-4 h-4 text-teal-700" />
                        Standard Requisition
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 group-hover:text-teal-700 transition">
                        New Requisition
                    </h2>
                    <p class="text-xs text-slate-500">
                        Request supplies or drugs during depot schedule window
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-200/60 text-teal-700 flex items-center justify-center shrink-0 group-hover:bg-teal-700 group-hover:text-white transition">
                    <x-filament::icon icon="heroicon-m-arrow-right" class="w-5 h-5" />
                </div>
            </a>

            {{-- B. Full-Width Red STAT Emergency Requisition Card --}}
            <a href="{{ url('/portal/requests/create?urgent=1') }}"
               class="min-h-[48px] p-6 rounded-xl bg-red-50/70 border border-red-200/80 shadow-[0_20px_40px_-10px_rgba(0,0,0,0.03)] hover:bg-red-50 hover:border-red-300 transition-all group flex items-center justify-between">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-red-700">
                        <x-filament::icon icon="heroicon-m-bolt" class="w-4 h-4 text-red-600" />
                        Priority: Immediate Emergency
                    </div>
                    <h2 class="text-xl font-extrabold text-red-950">
                        STAT Emergency Request
                    </h2>
                    <p class="text-xs text-red-700">
                        24/7 priority emergency fulfillment for critical patient care
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-600 text-white flex items-center justify-center shrink-0 shadow-xs group-hover:scale-105 transition">
                    <x-filament::icon icon="heroicon-m-bolt" class="w-6 h-6 animate-pulse" />
                </div>
            </a>
        </div>

        {{-- 3. Last 5 Requests Section --}}
        <div class="p-6 rounded-xl bg-white border border-slate-200/60 shadow-[0_20px_40px_-10px_rgba(0,0,0,0.03)] space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-clock" class="w-5 h-5 text-teal-700" />
                        Last 5 Requisitions
                    </h3>
                    <p class="text-xs text-slate-500">
                        Most recent requests submitted by {{ $data['unitName'] }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ url('/portal/items') }}"
                       class="min-h-[48px] inline-flex items-center text-xs font-semibold text-slate-600 hover:text-teal-700 transition">
                        Browse Catalog
                    </a>
                    <span class="text-slate-300">•</span>
                    <a href="{{ url('/portal/requests') }}"
                       class="min-h-[48px] inline-flex items-center text-xs font-semibold text-teal-700 hover:text-teal-800 transition">
                        View All My Requests &rarr;
                    </a>
                </div>
            </div>

            @if ($data['recentRequests']->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50/70 border-b border-slate-200/60 text-slate-500 font-bold uppercase text-[11px]">
                                <th class="py-3 px-3">Request #</th>
                                <th class="py-3 px-3">Date & Time</th>
                                <th class="py-3 px-3">Priority</th>
                                <th class="py-3 px-3">Items / Qty</th>
                                <th class="py-3 px-3">Status</th>
                                <th class="py-3 px-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($data['recentRequests'] as $req)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3.5 px-3 font-mono font-bold text-teal-800">
                                        {{ $req->request_number }}
                                    </td>
                                    <td class="py-3.5 px-3 text-slate-600 font-medium whitespace-nowrap">
                                        {{ $req->request_date->format('M j, Y • g:i A') }}
                                    </td>
                                    <td class="py-3.5 px-3">
                                        @if ($req->is_urgent)
                                            <span class="hecs-badge hecs-badge--stat">
                                                <x-filament::icon icon="heroicon-m-bolt" />
                                                STAT (Urgent)
                                            </span>
                                        @else
                                            <span class="hecs-badge hecs-badge--regular">
                                                <x-filament::icon icon="heroicon-m-calendar" />
                                                Regular
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <span class="font-bold text-slate-900 tabular-nums">{{ $req->lines->count() }} items</span>
                                        <span class="text-slate-500 text-[11px] block tabular-nums">({{ $req->total_requested }} units total)</span>
                                    </td>
                                    <td class="py-3.5 px-3">
                                        @if ($req->status->value === 'pending')
                                            <span class="hecs-badge hecs-badge--pending">
                                                <x-filament::icon icon="heroicon-m-clock" />
                                                Pending
                                            </span>
                                        @elseif ($req->status->value === 'partially_fulfilled')
                                            <span class="hecs-badge hecs-badge--partially-fulfilled">
                                                <x-filament::icon icon="heroicon-m-arrow-path" />
                                                Partly Fulfilled
                                            </span>
                                        @elseif ($req->status->value === 'fulfilled')
                                            <span class="hecs-badge hecs-badge--fulfilled">
                                                <x-filament::icon icon="heroicon-m-check-badge" />
                                                Fulfilled
                                            </span>
                                        @else
                                            <span class="hecs-badge hecs-badge--rejected">
                                                <x-filament::icon icon="heroicon-m-x-circle" />
                                                Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-right">
                                        <a href="{{ url('/portal/requests/' . $req->id) }}"
                                           class="min-h-[48px] inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-200/60 hover:bg-teal-100 transition">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                {{-- Friendly Empty State --}}
                <div class="p-8 text-center rounded-xl bg-slate-50/60 border border-dashed border-slate-200 space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 text-slate-400 mx-auto flex items-center justify-center shadow-xs">
                        <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-6 h-6" />
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-slate-800">No Requisitions Submitted Yet</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Your unit has not placed any supply requests. Tap "New Requisition" above to order medical supplies or drugs from the central depot.
                        </p>
                    </div>
                    <a href="{{ url('/portal/requests/create') }}"
                       class="min-h-[48px] inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-xs font-bold bg-teal-700 text-white hover:bg-teal-800 transition shadow-xs gap-1.5">
                        <x-filament::icon icon="heroicon-m-plus-circle" class="w-4 h-4" />
                        Create First Requisition
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
