@php
    $batches = $this->data['batches'] ?? [];
    $totalBatches = count($batches);
    $totalUnits = 0;
    $totalValue = 0;

    foreach ($batches as $batch) {
        $qty = (int) ($batch['quantity_received'] ?? 0);
        $cost = (float) ($batch['unit_cost'] ?? 0);
        $totalUnits += $qty;
        $totalValue += ($qty * $cost);
    }

    $canViewCost = auth()->user()?->canViewUnitCosts() ?? false;
@endphp

<div class="flex flex-wrap items-center gap-2.5 sm:gap-3 py-1">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200">
        <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
        </svg>
        <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">Total Batches:</span>
        <strong class="text-sm font-bold text-slate-900 dark:text-white tabular-nums">{{ $totalBatches }}</strong>
    </div>

    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200">
        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
        </svg>
        <span class="text-xs text-emerald-700 dark:text-emerald-400 font-medium">Total Units:</span>
        <strong class="text-sm font-bold text-emerald-950 dark:text-emerald-100 tabular-nums">{{ number_format($totalUnits) }}</strong>
    </div>

    @if ($canViewCost)
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800 text-teal-900 dark:text-teal-200">
            <span class="text-xs font-bold text-teal-700 dark:text-teal-400">₱</span>
            <span class="text-xs text-teal-700 dark:text-teal-400 font-medium">Total DR Value:</span>
            <strong class="text-sm font-bold text-teal-950 dark:text-teal-100 tabular-nums">₱ {{ number_format($totalValue, 2) }}</strong>
        </div>
    @endif
</div>
