<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-4 rounded-xl bg-teal-50 border border-teal-200">
            <h2 class="text-base font-bold text-teal-950">
                Roxas Memorial Provincial Hospital • Consignment Inventory Reports
            </h2>
            <p class="text-xs text-teal-800 mt-1">
                Generate and download official PDF ledgers and Excel/CSV spreadsheets for depot stock, near-expiry monitoring, supplier deliveries, ward distribution, and immutable stock card history.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- 1. Stock on Hand Report --}}
            <div class="p-6 rounded-xl border border-gray-200 bg-white shadow-xs flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-teal-50 text-teal-700 border border-teal-100">
                        <x-filament::icon icon="heroicon-o-archive-box" class="w-5 h-5" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">
                        Stock on Hand Report
                    </h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Complete inventory ledger of active consigned medical supplies and drugs in central storage, showing issuable stock, minimum alert levels, and batch valuations.
                    </p>
                </div>

                <form method="GET" action="{{ route('reports.stock-on-hand.pdf') }}" target="_blank" class="mt-6 pt-4 border-t border-gray-100 space-y-3">
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">Category</label>
                            <select name="category" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500" @disabled($scopedCategory !== null)>
                                @if ($scopedCategory)
                                    <option value="{{ $scopedCategory->value }}">{{ $scopedCategory->getLabel() }}</option>
                                @else
                                    <option value="">All Categories</option>
                                    <option value="supply">Medical Supplies</option>
                                    <option value="drug">Drugs &amp; Medicine</option>
                                @endif
                            </select>
                        </div>
                        <div class="flex items-end pb-1.5">
                            <label class="inline-flex items-center gap-2 text-xs text-gray-700 font-medium cursor-pointer">
                                <input type="checkbox" name="low_stock_only" value="1" class="rounded border-gray-300 text-teal-700 focus:ring-teal-500" />
                                <span>Below Minimum Level Only</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold bg-teal-700 text-white hover:bg-teal-800 transition shadow-xs">
                            <x-filament::icon icon="heroicon-m-document-arrow-down" class="w-4 h-4" />
                            Download PDF
                        </button>
                        <button type="submit" formaction="{{ route('reports.stock-on-hand.csv') }}" formtarget="_self"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition shadow-xs">
                            <x-filament::icon icon="heroicon-m-arrow-down-tray" class="w-4 h-4 text-emerald-600" />
                            Export to Excel / CSV
                        </button>
                    </div>
                </form>
            </div>

            {{-- 2. Near-Expiry Report (30/60/90 Days) --}}
            <div class="p-6 rounded-xl border border-rose-200 bg-white shadow-xs flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-rose-50 text-rose-700 border border-rose-100">
                        <x-filament::icon icon="heroicon-o-clock" class="w-5 h-5" />
                    </div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-gray-900">
                            Near-Expiry Report
                        </h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                            Early Warning
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Early warning audit list of central depot batches expiring within your chosen window (or already expired). Used by custodians to prioritize FEFO fulfillment and avoid expiration waste.
                    </p>
                </div>

                <form method="GET" action="{{ route('reports.near-expiry.pdf') }}" target="_blank" class="mt-6 pt-4 border-t border-gray-100 space-y-3">
                    <div class="text-xs">
                        <label class="block text-gray-700 text-[11px] font-medium mb-1">Expiry Window</label>
                        <select name="days" class="w-full text-xs rounded-lg border-gray-300 focus:border-rose-500 focus:ring-rose-500">
                            <option value="90" selected>Within 90 Days (Default)</option>
                            <option value="60">Within 60 Days</option>
                            <option value="30">Within 30 Days (Critical)</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold bg-rose-700 text-white hover:bg-rose-800 transition shadow-xs">
                            <x-filament::icon icon="heroicon-m-document-arrow-down" class="w-4 h-4" />
                            Download PDF
                        </button>
                        <button type="submit" formaction="{{ route('reports.near-expiry.csv') }}" formtarget="_self"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition shadow-xs">
                            <x-filament::icon icon="heroicon-m-arrow-down-tray" class="w-4 h-4 text-emerald-600" />
                            Export to Excel / CSV
                        </button>
                    </div>
                </form>
            </div>

            {{-- 3. Delivery Receipts (DR) History --}}
            <div class="p-6 rounded-xl border border-gray-200 bg-white shadow-xs flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-teal-50 text-teal-700 border border-teal-100">
                        <x-filament::icon icon="heroicon-o-truck" class="w-5 h-5" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">
                        Delivery Receipts (DR) History
                    </h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Chronological record of supplier deliveries received into RMPH central storage, with supplier breakdown, DR reference numbers, total units, and DR value.
                    </p>
                </div>

                <form method="GET" action="{{ route('reports.deliveries.pdf') }}" target="_blank" class="mt-6 pt-4 border-t border-gray-100 space-y-3">
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">From Date</label>
                            <input type="date" name="from_date" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500" />
                        </div>
                        <div>
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">To Date</label>
                            <input type="date" name="to_date" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500" />
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">Accredited Consignor</label>
                            <select name="consignor_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">All Consignors</option>
                                @foreach ($consignors as $consignor)
                                    <option value="{{ $consignor->id }}">{{ $consignor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold bg-teal-700 text-white hover:bg-teal-800 transition shadow-xs">
                            <x-filament::icon icon="heroicon-m-document-arrow-down" class="w-4 h-4" />
                            Download PDF
                        </button>
                        <button type="submit" formaction="{{ route('reports.deliveries.csv') }}" formtarget="_self"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition shadow-xs">
                            <x-filament::icon icon="heroicon-m-arrow-down-tray" class="w-4 h-4 text-emerald-600" />
                            Export Excel
                        </button>
                    </div>
                </form>
            </div>

            {{-- 4. Unit Requisitions History --}}
            <div class="p-6 rounded-xl border border-gray-200 bg-white shadow-xs flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-teal-50 text-teal-700 border border-teal-100">
                        <x-filament::icon icon="heroicon-o-building-office-2" class="w-5 h-5" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">
                        Requisitions History by Unit
                    </h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Audit ledger of requisitions across the 6 units (CSR, HDU, Gugma Dialysis, Pharmacies, Yakap), tracking fulfillment status, quantities requested vs issued, and STAT urgencies.
                    </p>
                </div>

                <form method="GET" action="{{ route('reports.unit-requests.pdf') }}" target="_blank" class="mt-6 pt-4 border-t border-gray-100 space-y-3">
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">From Date</label>
                            <input type="date" name="from_date" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500" />
                        </div>
                        <div>
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">To Date</label>
                            <input type="date" name="to_date" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500" />
                        </div>
                        <div>
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">Filter by Unit</label>
                            <select name="unit_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">All 6 Units</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">Priority</label>
                            <select name="is_urgent" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">All Priorities</option>
                                <option value="0">Regular Requisitions</option>
                                <option value="1">Urgent (STAT) Only</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">Status</label>
                            <select name="status" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">All Statuses</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}">{{ $status->getLabel() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold bg-teal-700 text-white hover:bg-teal-800 transition shadow-xs">
                            <x-filament::icon icon="heroicon-m-document-arrow-down" class="w-4 h-4" />
                            Download PDF
                        </button>
                        <button type="submit" formaction="{{ route('reports.unit-requests.csv') }}" formtarget="_self"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition shadow-xs">
                            <x-filament::icon icon="heroicon-m-arrow-down-tray" class="w-4 h-4 text-emerald-600" />
                            Export Excel
                        </button>
                    </div>
                </form>
            </div>

            {{-- 5. Immutable Stock Card Movement History --}}
            <div class="p-6 rounded-xl border border-gray-200 bg-white shadow-xs flex flex-col justify-between md:col-span-2">
                <div class="space-y-2">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-teal-50 text-teal-700 border border-teal-100">
                        <x-filament::icon icon="heroicon-o-arrows-right-left" class="w-5 h-5" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">
                        Stock Card Movement History
                    </h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Immutable audit trail of every RECEIVE, ISSUE, and physical ADJUST transaction with running batch balances, reference numbers, and staff attribution.
                    </p>
                </div>

                <form method="GET" action="{{ route('reports.stock-movements.pdf') }}" target="_blank" class="mt-6 pt-4 border-t border-gray-100 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 text-xs">
                        <div>
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">From Date</label>
                            <input type="date" name="from_date" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500" />
                        </div>
                        <div>
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">To Date</label>
                            <input type="date" name="to_date" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500" />
                        </div>
                        <div>
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">Movement Type</label>
                            <select name="type" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">All Movements</option>
                                <option value="RECEIVE">RECEIVE</option>
                                <option value="ISSUE">ISSUE</option>
                                <option value="ADJUST">ADJUST</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-[11px] font-medium mb-1">Catalog Item</label>
                            <select name="item_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                                <option value="">All Items</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">[{{ $item->code }}] {{ $item->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold bg-teal-700 text-white hover:bg-teal-800 transition shadow-xs">
                            <x-filament::icon icon="heroicon-m-document-arrow-down" class="w-4 h-4" />
                            Download PDF
                        </button>
                        <button type="submit" formaction="{{ route('reports.stock-movements.csv') }}" formtarget="_self"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition shadow-xs">
                            <x-filament::icon icon="heroicon-m-arrow-down-tray" class="w-4 h-4 text-emerald-600" />
                            Export Excel / CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-filament-panels::page>
