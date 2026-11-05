<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Bar Card -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                <span>🔍</span> Report Filters & Timeframe
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Time Period</label>
                    <select wire:model.live="preset" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-teal-500 focus:ring-teal-500">
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="this_year">This Year</option>
                        <option value="all_time">All Time</option>
                        <option value="custom">Custom Date Range</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Date From</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-teal-500 focus:ring-teal-500" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Date To</label>
                    <input type="date" wire:model.live="dateTo" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-teal-500 focus:ring-teal-500" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Department Stream</label>
                    <select wire:model.live="department" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-teal-500 focus:ring-teal-500">
                        <option value="all">All Consignments</option>
                        <option value="pharmacy">Pharmacy Only</option>
                        <option value="medical_supplies">Medical Supplies Only</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase mb-1">Consignor (Supplier)</label>
                    <select wire:model.live="supplierId" class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-teal-500 focus:ring-teal-500">
                        <option value="all">All Consignors</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- KPI Metric Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs font-bold uppercase tracking-wider text-teal-600 dark:text-teal-400">Total Inwards Received</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100 mt-2">₱{{ number_format($totalReceivedCost, 2) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Consignment deliveries during selected period</div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Total Consumed / Issued</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100 mt-2">₱{{ number_format($totalIssuedCost, 2) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hospital consumption (incurred liability)</div>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Ending Consignment Valuation</div>
                <div class="text-2xl font-black text-gray-900 dark:text-gray-100 mt-2">₱{{ number_format($totalEndingValuation, 2) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total physical goods on hand at period close</div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-750">
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-gray-100 text-sm">Detailed Inventory Movement & Valuation</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Period: {{ $dateFrom }} to {{ $dateTo }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="exportCsv" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-sm active:scale-95 disabled:opacity-50">
                        <span wire:loading.remove wire:target="exportCsv">📊 Export CSV</span>
                        <span wire:loading wire:target="exportCsv">Generating...</span>
                    </button>

                    <a href="{{ route('inventory-reports.print', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'department' => $department, 'supplier_id' => $supplierId]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition shadow-sm active:scale-95">
                        🖨️ Official Audit Print
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 font-semibold uppercase border-b border-gray-200 dark:border-gray-700">
                            <th class="p-3">SKU / Item</th>
                            <th class="p-3">Stream</th>
                            <th class="p-3">UOM</th>
                            <th class="p-3 text-right">Beg. Qty</th>
                            <th class="p-3 text-right text-teal-600 dark:text-teal-400">+ In (Qty)</th>
                            <th class="p-3 text-right text-amber-600 dark:text-amber-400">- Out (Qty)</th>
                            <th class="p-3 text-right font-bold">End Qty</th>
                            <th class="p-3 text-right">Unit Cost</th>
                            <th class="p-3 text-right font-bold text-emerald-600 dark:text-emerald-400">End Valuation</th>
                            <th class="p-3 text-center">Ledger</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($reportRows as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="p-3">
                                    <div class="font-bold text-gray-900 dark:text-gray-100">{{ $row['item_code'] }}</div>
                                    <div class="text-gray-600 dark:text-gray-400">{{ $row['name'] }}</div>
                                    @if($row['generic_name'])
                                        <div class="text-gray-400 text-[10px]">Generic: {{ $row['generic_name'] }}</div>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $row['department'] === 'pharmacy' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300' }}">
                                        {{ $row['department'] === 'pharmacy' ? 'Pharmacy' : 'Med Supply' }}
                                    </span>
                                </td>
                                <td class="p-3 text-gray-600 dark:text-gray-400">{{ ucfirst($row['uom']) }}</td>
                                <td class="p-3 text-right text-gray-600 dark:text-gray-400">{{ $row['beginning_balance'] }}</td>
                                <td class="p-3 text-right font-semibold text-teal-600 dark:text-teal-400">{{ $row['received_qty'] > 0 ? '+' . $row['received_qty'] : '—' }}</td>
                                <td class="p-3 text-right font-semibold text-amber-600 dark:text-amber-400">{{ $row['issued_qty'] > 0 ? '-' . $row['issued_qty'] : '—' }}</td>
                                <td class="p-3 text-right font-bold text-gray-900 dark:text-gray-100">{{ $row['ending_balance'] }}</td>
                                <td class="p-3 text-right text-gray-600 dark:text-gray-400">₱{{ number_format($row['unit_cost'], 2) }}</td>
                                <td class="p-3 text-right font-bold text-emerald-600 dark:text-emerald-400">₱{{ number_format($row['ending_valuation'], 2) }}</td>
                                <td class="p-3 text-center">
                                    <a href="{{ route('stockcard.print', ['item' => $row['id']]) }}" target="_blank" class="inline-flex items-center px-2 py-1 text-[11px] font-semibold text-teal-700 bg-teal-50 dark:bg-teal-900/30 dark:text-teal-300 rounded hover:bg-teal-100 dark:hover:bg-teal-900/60 transition">
                                        Stockcard ↗
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-8 text-center text-gray-400">
                                    No records match the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 dark:bg-gray-700 font-bold border-t-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                            <td colspan="4" class="p-3 text-right uppercase text-xs">Summary Totals:</td>
                            <td class="p-3 text-right text-teal-600 dark:text-teal-400">₱{{ number_format($totalReceivedCost, 2) }}</td>
                            <td class="p-3 text-right text-amber-600 dark:text-amber-400">₱{{ number_format($totalIssuedCost, 2) }}</td>
                            <td colspan="2" class="p-3 text-right uppercase text-xs">Total Ending Value:</td>
                            <td class="p-3 text-right text-emerald-600 dark:text-emerald-400">₱{{ number_format($totalEndingValuation, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
