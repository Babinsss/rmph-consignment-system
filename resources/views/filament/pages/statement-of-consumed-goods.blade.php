<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Control Header & Filters -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300 uppercase tracking-wider mb-1">
                        Consignment Liquidation Voucher
                    </span>
                    <h3 class="text-lg font-black text-gray-900 dark:text-gray-100">
                        Statement of Consumed Goods (StCG)
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Official monthly billing statement submitted to consignors for hospital payment liquidation.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="exportCsv" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition shadow-sm active:scale-95 disabled:opacity-50">
                        <span wire:loading.remove wire:target="exportCsv">📊 Export StCG CSV</span>
                        <span wire:loading wire:target="exportCsv">Generating...</span>
                    </button>
                    @if($supplierId)
                        <a href="{{ route('stcg.print', ['supplier' => $supplierId, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'stcg_number' => $stcgNumber]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition shadow-sm active:scale-95">
                            🖨️ Print StCG Voucher
                        </a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Select Consignor</label>
                    <select wire:model.live="supplierId" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-teal-500 focus:ring-teal-500">
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Liquidation Period</label>
                    <select wire:model.live="preset" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-teal-500 focus:ring-teal-500">
                        <option value="this_month">Current Month ({{ now()->format('M Y') }})</option>
                        <option value="last_month">Previous Month ({{ now()->subMonth()->format('M Y') }})</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">StCG Reference Number</label>
                    <input type="text" wire:model.live="stcgNumber" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 font-mono font-bold text-teal-600" />
                </div>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Consignor Details</div>
                <div class="text-lg font-black text-gray-900 dark:text-gray-100 mt-2 truncate">{{ $currentSupplier?->name ?? 'N/A' }}</div>
                <div class="text-xs text-teal-600 dark:text-teal-400 mt-1">Code: {{ $currentSupplier?->code ?? 'N/A' }} • {{ $currentSupplier?->payment_terms ?? '30 days' }}</div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Total Consumed Units</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100 mt-2">{{ number_format($totalQuantity) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Dispensed to in-patient wards & OR units</div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Total Amount Payable (Liability)</div>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">₱{{ number_format($totalPayable, 2) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Payable to consignor for liquidated consumption</div>
            </div>
        </div>

        <!-- StCG Itemized Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-750">
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-gray-100 text-sm">Itemized Consumed Goods Schedule</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Period: {{ $dateFrom }} to {{ $dateTo }}</p>
                </div>
                <span class="text-xs font-mono font-bold text-teal-700 bg-teal-50 dark:bg-teal-900/30 px-3 py-1 rounded-md">
                    {{ $stcgNumber }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 font-semibold uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="p-3">Item Code</th>
                            <th class="p-3">Item Description</th>
                            <th class="p-3">Category</th>
                            <th class="p-3">UOM</th>
                            <th class="p-3 text-right">Consignor Cost</th>
                            <th class="p-3 text-right">Selling Price</th>
                            <th class="p-3 text-right font-bold text-amber-600 dark:text-amber-400">Qty Consumed</th>
                            <th class="p-3 text-right font-bold text-emerald-600 dark:text-emerald-400">Total Payable</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($consumedRows as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="p-3 font-bold text-gray-900 dark:text-gray-100">{{ $row['item_code'] }}</td>
                                <td class="p-3 font-medium text-gray-900 dark:text-gray-200">
                                    {{ $row['item_name'] }}
                                    @if($row['generic_name'])
                                        <div class="text-[10px] text-gray-400">Generic: {{ $row['generic_name'] }}</div>
                                    @endif
                                </td>
                                <td class="p-3 text-gray-500 dark:text-gray-400">{{ $row['category'] }}</td>
                                <td class="p-3 text-gray-500 dark:text-gray-400">{{ ucfirst($row['uom']) }}</td>
                                <td class="p-3 text-right text-gray-700 dark:text-gray-300">₱{{ number_format($row['unit_cost'], 2) }}</td>
                                <td class="p-3 text-right text-gray-500 dark:text-gray-400">₱{{ number_format($row['selling_price'], 2) }}</td>
                                <td class="p-3 text-right font-bold text-amber-600 dark:text-amber-400">{{ number_format($row['quantity_consumed']) }}</td>
                                <td class="p-3 text-right font-black text-emerald-600 dark:text-emerald-400">₱{{ number_format($row['total_amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-gray-400">
                                    No consumed goods recorded for {{ $currentSupplier?->name }} during this billing period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 dark:bg-gray-700 font-bold border-t-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                            <td colspan="6" class="p-3 text-right uppercase text-xs">Total Consumed Units & Payable Amount:</td>
                            <td class="p-3 text-right text-amber-600 dark:text-amber-400">{{ number_format($totalQuantity) }}</td>
                            <td class="p-3 text-right text-emerald-600 dark:text-emerald-400 text-sm">₱{{ number_format($totalPayable, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
