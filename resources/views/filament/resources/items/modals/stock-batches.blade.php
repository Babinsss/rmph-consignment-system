@php
    $batches = $item->batches()
        ->where('quantity_remaining', '>', 0)
        ->where('is_active', true)
        ->with('delivery.consignor')
        ->orderBy('expiration_date', 'asc')
        ->get();

    $canViewCost = auth()->user()?->canViewUnitCosts() ?? false;
@endphp

<div class="space-y-4">
    @if ($batches->isEmpty())
        <div class="py-8 text-center text-slate-500">
            <svg class="w-10 h-10 mx-auto text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
            <p class="text-sm font-medium">No active stock batches found for this item in the central depot.</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-700">
            <table class="w-full text-left text-xs divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-3 py-2.5">DR Number</th>
                        <th class="px-3 py-2.5">Delivering Consignor</th>
                        <th class="px-3 py-2.5">Lot / Batch #</th>
                        <th class="px-3 py-2.5">Expiration Date</th>
                        @if ($canViewCost)
                            <th class="px-3 py-2.5 text-right">Unit Cost</th>
                        @endif
                        <th class="px-3 py-2.5 text-right">Remaining Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200">
                    @foreach ($batches as $batch)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                            <td class="px-3 py-2.5 font-medium text-slate-900 dark:text-white">
                                {{ $batch->delivery?->dr_number ?? '—' }}
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="inline-flex items-center gap-1 font-medium {{ $batch->delivery?->consignor_id === $item->consignor_id ? 'text-slate-700 dark:text-slate-300' : 'text-teal-700 dark:text-teal-300' }}">
                                    {{ $batch->delivery?->consignor?->name ?? '—' }}
                                    @if ($batch->delivery?->consignor_id !== $item->consignor_id)
                                        <span class="text-[10px] px-1.5 py-0.2 bg-teal-50 dark:bg-teal-950 text-teal-700 dark:text-teal-300 rounded border border-teal-200 dark:border-teal-800">Cross-Supplier</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-3 py-2.5 font-mono text-slate-700 dark:text-slate-300">
                                {{ $batch->lot_number }}
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="tabular-nums {{ $batch->is_expiring_soon ? 'text-amber-700 font-semibold' : '' }}">
                                    {{ $batch->expiration_date->format('M d, Y') }}
                                </span>
                            </td>
                            @if ($canViewCost)
                                <td class="px-3 py-2.5 text-right tabular-nums">
                                    ₱ {{ number_format($batch->unit_cost, 2) }}
                                </td>
                            @endif
                            <td class="px-3 py-2.5 text-right font-bold text-emerald-700 dark:text-emerald-400 tabular-nums">
                                {{ number_format($batch->quantity_remaining) }} {{ $item->unit_of_measure }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 dark:bg-slate-800 font-semibold text-slate-900 dark:text-white border-t border-slate-200 dark:border-slate-700">
                    <tr>
                        <td colspan="{{ $canViewCost ? 5 : 4 }}" class="px-3 py-2 text-right">Total Stock in Depot:</td>
                        <td class="px-3 py-2 text-right text-emerald-800 dark:text-emerald-300 text-sm font-bold tabular-nums">
                            {{ number_format($batches->sum('quantity_remaining')) }} {{ $item->unit_of_measure }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>
