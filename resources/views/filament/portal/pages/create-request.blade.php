<x-filament-panels::page>
    @php
        $availableItems = $this->getAvailableItems();
        $cartItems = $this->cart_items;
        $totalUnits = $this->total_units;
        $distinctItemsCount = count($this->cart);
        $user = auth()->user();
        $isWindowOpen = $this->is_window_open;
    @endphp

    <div class="space-y-6">
        {{-- 1. Top Bar: Unit Context, Back Navigation, and Urgency Switch --}}
        <div class="p-4 sm:p-6 rounded-xl bg-white border border-slate-200/60 shadow-[0_20px_40px_-10px_rgba(0,0,0,0.03)] flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <a href="{{ url('/portal') }}"
                       class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-teal-700 transition">
                        <x-filament::icon icon="heroicon-m-arrow-left" class="w-4 h-4" />
                        Back to Portal Home
                    </a>
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider bg-teal-50 text-teal-800 border border-teal-200/60 font-mono">
                        {{ $user->unit?->code ?? 'WARD' }}
                    </span>
                    <span class="text-xs text-slate-500 font-medium">
                        {{ $user->unit?->name ?? 'Requesting Unit' }}
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
                    @if ($is_urgent)
                        <span class="inline-flex items-center gap-2 text-red-700">
                            <x-filament::icon icon="heroicon-m-bolt" class="w-7 h-7 text-red-600 animate-pulse" />
                            STAT Emergency Requisition Slip
                        </span>
                    @else
                        <span>New Ward Requisition Slip</span>
                    @endif
                </h1>
                <p class="text-xs text-slate-500">
                    Staff in charge: <strong class="text-slate-800 font-semibold">{{ $user->name }}</strong> • Central Storage Depot Fulfillment
                </p>
            </div>

            {{-- Mode Switcher Button --}}
            <div class="flex items-center gap-3">
                @if ($is_urgent)
                    <button type="button"
                            wire:click="toggleUrgent"
                            class="min-h-[48px] px-4 py-2.5 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-200/80 hover:bg-slate-50 hover:border-slate-300 transition flex items-center gap-2 shadow-xs">
                        <x-filament::icon icon="heroicon-m-calendar" class="w-4 h-4 text-slate-500" />
                        Switch to Standard Requisition
                    </button>
                @else
                    <button type="button"
                            wire:click="toggleUrgent"
                            class="min-h-[48px] px-4 py-2.5 rounded-xl text-xs font-bold bg-red-50 text-red-700 border border-red-200/80 hover:bg-red-100 hover:border-red-300 transition flex items-center gap-2 shadow-xs">
                        <x-filament::icon icon="heroicon-m-bolt" class="w-4 h-4 text-red-600 animate-pulse" />
                        Switch to STAT (Urgent) Request
                    </button>
                @endif
            </div>
        </div>

        {{-- 2. Window Warning Banner (When Regular Window is Closed) --}}
        @if (! $is_urgent && ! $isWindowOpen)
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-amber-900">
                <div class="flex items-start gap-3">
                    <x-filament::icon icon="heroicon-m-exclamation-triangle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold">Regular Requisition Window is Currently Closed</p>
                        <p class="text-xs text-amber-800">
                            Regular requests are accepted Monday to Friday before 5:00 PM. For urgent patient needs, switch to a STAT Emergency Requisition.
                        </p>
                    </div>
                </div>
                <button type="button"
                        wire:click="toggleUrgent"
                        class="min-h-[48px] px-4 py-2 rounded-xl text-xs font-bold bg-red-600 text-white hover:bg-red-700 transition shrink-0 flex items-center justify-center gap-1.5 shadow-xs">
                    <x-filament::icon icon="heroicon-m-bolt" class="w-4 h-4" />
                    Switch to STAT Request
                </button>
            </div>
        @endif

        {{-- 3. Full-Width Red STAT Emergency Card (When is_urgent is true) --}}
        @if ($is_urgent)
            <div class="p-6 rounded-xl bg-red-50/80 border-2 border-red-300 shadow-[0_20px_40px_-10px_rgba(0,0,0,0.03)] space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-red-200/80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-600 text-white flex items-center justify-center shadow-xs shrink-0">
                            <x-filament::icon icon="heroicon-m-bolt" class="w-6 h-6 animate-pulse" />
                        </div>
                        <div>
                            <h2 class="text-base font-extrabold text-red-950 uppercase tracking-wide">
                                STAT Emergency Protocol Active
                            </h2>
                            <p class="text-xs text-red-700">
                                This requisition will bypass normal scheduling for immediate depot fulfillment. Clinical justification is mandatory.
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                        24/7 Rapid Response
                    </span>
                </div>

                {{-- Preset Reason Chips --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-red-900 uppercase tracking-wider">
                        Select Clinical Reason Preset (Tap to Insert)
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($presetReasons as $preset)
                            <button type="button"
                                    wire:click="selectPresetReason('{{ $preset }}')"
                                    class="min-h-[48px] px-3.5 py-2 rounded-xl text-xs font-semibold bg-white text-red-800 border border-red-200 hover:bg-red-100/70 hover:border-red-300 transition flex items-center gap-1.5 shadow-xs">
                                <x-filament::icon icon="heroicon-m-plus" class="w-3.5 h-3.5 text-red-600" />
                                <span>{{ $preset }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Free Text Clinical Justification --}}
                <div class="space-y-1.5">
                    <label for="urgent_reason" class="block text-xs font-bold text-red-950">
                        Detailed Clinical Justification <span class="text-red-600">*</span>
                        <span class="text-[11px] font-normal text-red-700">(Minimum 10 characters required)</span>
                    </label>
                    <textarea id="urgent_reason"
                              wire:model.live.debounce.300ms="urgent_reason"
                              rows="3"
                              placeholder="Describe the clinical situation (e.g. Acute Trauma Emergency: polytrauma patient needing rapid thoracostomy insertion supplies in HDU)..."
                              class="w-full rounded-xl border-red-300 bg-white text-slate-900 text-xs shadow-xs focus:border-red-600 focus:ring-red-600 placeholder:text-slate-400 p-3"></textarea>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-[11px] text-red-700">
                        <span class="flex items-center gap-1">
                            <x-filament::icon icon="heroicon-m-shield-check" class="w-3.5 h-3.5 text-red-600" />
                            <strong>Privacy Notice:</strong> Do NOT enter identifiable patient names or MRN numbers. Use clinical conditions only.
                        </span>
                        <span class="font-mono text-red-800 font-bold tabular-nums">
                            {{ mb_strlen(trim($urgent_reason)) }} / 10 characters minimum
                        </span>
                    </div>

                    @error('urgent_reason')
                        <p class="text-xs font-semibold text-red-700 mt-1 flex items-center gap-1">
                            <x-filament::icon icon="heroicon-m-exclamation-circle" class="w-4 h-4" />
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        @endif

        {{-- 4. Two-Pane Request Builder --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- ================= LEFT PANE: CATALOG BROWSER (8 Cols) ================= --}}
            <div class="lg:col-span-7 xl:col-span-8 space-y-4">
                {{-- Search & Category Filters --}}
                <div class="p-4 rounded-xl bg-white border border-slate-200/60 shadow-[0_20px_40px_-10px_rgba(0,0,0,0.03)] space-y-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <x-filament::icon icon="heroicon-m-magnifying-glass" class="w-5 h-5" />
                        </div>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search items by description, generic name, or item code..."
                               class="w-full min-h-[48px] pl-11 pr-10 rounded-xl border-slate-200/80 bg-slate-50/50 text-slate-900 text-xs focus:bg-white focus:border-teal-700 focus:ring-teal-700 transition placeholder:text-slate-400">
                        @if (filled($search))
                            <button type="button"
                                    wire:click="$set('search', '')"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <x-filament::icon icon="heroicon-m-x-mark" class="w-5 h-5" />
                            </button>
                        @endif
                    </div>

                    {{-- Category Filter Pills --}}
                    <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-slate-100">
                        <span class="text-xs font-bold text-slate-500 mr-1">Filter:</span>
                        <button type="button"
                                wire:click="$set('categoryFilter', 'all')"
                                class="min-h-[48px] px-3.5 py-1.5 rounded-xl text-xs font-semibold transition {{ $categoryFilter === 'all' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            All Consigned Items
                        </button>
                        <button type="button"
                                wire:click="$set('categoryFilter', 'supply')"
                                class="min-h-[48px] px-3.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $categoryFilter === 'supply' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            <x-filament::icon icon="heroicon-m-cube" class="w-3.5 h-3.5" />
                            Medical Supplies
                        </button>
                        <button type="button"
                                wire:click="$set('categoryFilter', 'drug')"
                                class="min-h-[48px] px-3.5 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $categoryFilter === 'drug' ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            <x-filament::icon icon="heroicon-m-beaker" class="w-3.5 h-3.5" />
                            Drugs and Medicines
                        </button>
                    </div>
                </div>

                {{-- Items Catalog Grid --}}
                @if ($availableItems->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($availableItems as $item)
                            @php
                                $inCart = isset($cart[$item->id]);
                                $currentQty = $cart[$item->id] ?? 0;
                                $stock = $item->current_stock ?? $item->batches()->sum('quantity_remaining');
                            @endphp
                            <div class="p-4 rounded-xl bg-white border {{ $inCart ? 'border-teal-500 ring-1 ring-teal-500/30' : 'border-slate-200/60' }} shadow-[0_20px_40px_-10px_rgba(0,0,0,0.03)] hover:border-slate-300 transition flex flex-col justify-between gap-3">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider font-mono {{ $item->category->value === 'supply' ? 'bg-sky-50 text-sky-800 border border-sky-200/60' : 'bg-purple-50 text-purple-800 border border-purple-200/60' }}">
                                            @if ($item->category->value === 'supply')
                                                <x-filament::icon icon="heroicon-m-cube" class="w-3 h-3 text-sky-700" />
                                                Supply
                                            @else
                                                <x-filament::icon icon="heroicon-m-beaker" class="w-3 h-3 text-purple-700" />
                                                Drug
                                            @endif
                                        </span>
                                        <span class="text-[11px] font-mono text-slate-400">
                                            {{ $item->code }}
                                        </span>
                                    </div>

                                    <h3 class="text-sm font-bold text-slate-900 leading-snug">
                                        {{ $item->description }}
                                    </h3>

                                    <div class="text-[11px] text-slate-500 space-y-0.5">
                                        <div>Consignor: <strong class="text-slate-700 font-semibold">{{ $item->consignor?->name ?? 'Standard Depot' }}</strong></div>
                                        <div>Packaging: <span class="font-medium text-slate-700">{{ $item->unit_of_measure }}</span></div>
                                    </div>

                                    {{-- Stock On Hand (Depot) --}}
                                    <div class="pt-1 text-xs">
                                        @if ($stock > 0)
                                            <span class="inline-flex items-center gap-1.5 text-emerald-800 font-medium">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                <strong class="tabular-nums font-bold">{{ $stock }}</strong> {{ $item->unit_of_measure }} available in depot
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-rose-700 font-medium">
                                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                                Depot stock depleted (request subject to supplier backorder)
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action Button (48px Touch Target) --}}
                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                    @if ($inCart)
                                        <div class="flex items-center gap-1.5 w-full justify-between">
                                            <div class="flex items-center gap-1">
                                                <button type="button"
                                                        wire:click="decrementQuantity({{ $item->id }})"
                                                        class="min-w-[48px] min-h-[48px] rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-base transition">
                                                    <x-filament::icon icon="heroicon-m-minus" class="w-4 h-4" />
                                                </button>
                                                <span class="w-12 text-center font-bold tabular-nums text-sm text-teal-800">
                                                    {{ $currentQty }}
                                                </span>
                                                <button type="button"
                                                        wire:click="incrementQuantity({{ $item->id }})"
                                                        class="min-w-[48px] min-h-[48px] rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-base transition">
                                                    <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4" />
                                                </button>
                                            </div>
                                            <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200/60">
                                                In Request Slip
                                            </span>
                                        </div>
                                    @else
                                        <button type="button"
                                                wire:click="addToCart({{ $item->id }})"
                                                class="min-h-[48px] w-full px-4 py-2.5 rounded-xl text-xs font-bold bg-teal-700 hover:bg-teal-800 text-white transition flex items-center justify-center gap-1.5 shadow-xs">
                                            <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4" />
                                            Add to Request Slip
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Catalog Empty State --}}
                    <div class="p-10 text-center rounded-xl bg-white border border-dashed border-slate-200 space-y-3">
                        <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 text-slate-400 mx-auto flex items-center justify-center">
                            <x-filament::icon icon="heroicon-o-magnifying-glass" class="w-6 h-6" />
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-slate-800">No Matching Consigned Items Found</h4>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                We couldn't find items matching "<strong class="text-slate-700">{{ $search }}</strong>". Try searching with a different term or clear the filter.
                            </p>
                        </div>
                        <button type="button"
                                wire:click="$set('search', ''); $set('categoryFilter', 'all')"
                                class="min-h-[48px] px-4 py-2 rounded-xl text-xs font-semibold text-teal-700 bg-teal-50 border border-teal-200/60 hover:bg-teal-100 transition">
                            Reset Search & Filters
                        </button>
                    </div>
                @endif
            </div>

            {{-- ================= RIGHT PANE: STICKY REQUISITION SLIP / CART (4 Cols) ================= --}}
            <div class="lg:col-span-5 xl:col-span-4">
                <div class="sticky top-6 p-5 rounded-xl bg-white border border-slate-200/60 shadow-[0_20px_40px_-10px_rgba(0,0,0,0.03)] space-y-5">
                    {{-- Cart Header --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg {{ $is_urgent ? 'bg-red-50 text-red-600' : 'bg-teal-50 text-teal-700' }} flex items-center justify-center font-bold text-sm">
                                <x-filament::icon icon="heroicon-o-clipboard-document-check" class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">
                                    Requisition Slip
                                </h2>
                                <p class="text-[11px] text-slate-500">
                                    {{ $distinctItemsCount }} distinct items selected
                                </p>
                            </div>
                        </div>

                        @if ($distinctItemsCount > 0)
                            <button type="button"
                                    wire:click="clearCart"
                                    class="text-xs text-slate-400 hover:text-rose-600 font-medium transition">
                                Clear All
                            </button>
                        @endif
                    </div>

                    {{-- Cart Items List --}}
                    @if (empty($cartItems))
                        {{-- Friendly Empty Cart State --}}
                        <div class="py-8 text-center space-y-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 text-slate-400 mx-auto flex items-center justify-center">
                                <x-filament::icon icon="heroicon-o-shopping-cart" class="w-6 h-6" />
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-slate-700">Requisition Slip is Empty</p>
                                <p class="text-[11px] text-slate-500 max-w-xs mx-auto">
                                    Tap <strong>"+ Add to Request Slip"</strong> on any item in the catalog to build your order.
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="space-y-3 max-h-[380px] overflow-y-auto pr-1 divide-y divide-slate-100">
                            @foreach ($cartItems as $row)
                                @php
                                    $item = $row['item'];
                                    $qty = $row['quantity'];
                                @endphp
                                <div class="pt-3 first:pt-0 space-y-2">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-900 leading-snug">
                                                {{ $item->description }}
                                            </h4>
                                            <p class="text-[11px] text-slate-500">
                                                {{ $item->code }} • {{ $item->unit_of_measure }}
                                            </p>
                                        </div>
                                        <button type="button"
                                                wire:click="removeFromCart({{ $item->id }})"
                                                class="min-w-[48px] min-h-[48px] flex items-center justify-center text-slate-400 hover:text-rose-600 transition -mr-2">
                                            <x-filament::icon icon="heroicon-m-trash" class="w-4 h-4" />
                                        </button>
                                    </div>

                                    {{-- Large 48px + / - Steppers --}}
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] text-slate-500 font-medium">Quantity to Request:</span>
                                        <div class="flex items-center gap-1.5">
                                            <button type="button"
                                                    wire:click="decrementQuantity({{ $item->id }})"
                                                    class="min-w-[48px] min-h-[48px] rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-base transition">
                                                <x-filament::icon icon="heroicon-m-minus" class="w-4 h-4" />
                                            </button>
                                            <span class="w-10 text-center font-bold tabular-nums text-sm text-slate-900">
                                                {{ $qty }}
                                            </span>
                                            <button type="button"
                                                    wire:click="incrementQuantity({{ $item->id }})"
                                                    class="min-w-[48px] min-h-[48px] rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-base transition">
                                                <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Requisition Notes / Instructions --}}
                    <div class="pt-3 border-t border-slate-100 space-y-1.5">
                        <label for="notes" class="block text-xs font-bold text-slate-700">
                            Ward Delivery Notes / Station Notes (Optional)
                        </label>
                        <textarea id="notes"
                                  wire:model.live.debounce.300ms="notes"
                                  rows="2"
                                  placeholder="e.g., Deliver to Station 2 nurse desk; needed for morning procedures..."
                                  class="w-full rounded-xl border-slate-200/80 bg-slate-50/50 text-slate-900 text-xs shadow-xs focus:bg-white focus:border-teal-700 focus:ring-teal-700 placeholder:text-slate-400 p-2.5"></textarea>
                    </div>

                    {{-- Live Totals Summary (NO FINANCIAL/UNIT COST LEAKED) --}}
                    <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-200/60 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium">Distinct Items:</span>
                            <span class="font-bold tabular-nums text-slate-800">{{ $distinctItemsCount }} items</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium">Total Quantity:</span>
                            <span class="font-extrabold tabular-nums text-teal-900 text-sm">{{ $totalUnits }} units</span>
                        </div>
                        <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-200/60">
                            <span class="text-slate-500 font-medium">Fulfillment Depot:</span>
                            <span class="font-semibold text-slate-700">RMPH Central Storage</span>
                        </div>
                    </div>

                    @error('cart')
                        <p class="text-xs font-semibold text-red-700 flex items-center gap-1">
                            <x-filament::icon icon="heroicon-m-exclamation-circle" class="w-4 h-4" />
                            {{ $message }}
                        </p>
                    @enderror

                    @error('window')
                        <p class="text-xs font-semibold text-red-700 flex items-center gap-1">
                            <x-filament::icon icon="heroicon-m-exclamation-circle" class="w-4 h-4" />
                            {{ $message }}
                        </p>
                    @enderror

                    {{-- Primary Submit Button (48px Touch Target) --}}
                    <div>
                        <button type="button"
                                wire:click="openConfirm"
                                @if (empty($cart)) disabled @endif
                                class="min-h-[48px] w-full px-5 py-3 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-xs {{ empty($cart) ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : ($is_urgent ? 'bg-red-600 hover:bg-red-700 text-white shadow-red-200' : 'bg-teal-700 hover:bg-teal-800 text-white shadow-teal-200') }}">
                            @if ($is_urgent)
                                <x-filament::icon icon="heroicon-m-bolt" class="w-5 h-5 text-white" />
                                <span>Review & Submit STAT Request</span>
                            @else
                                <x-filament::icon icon="heroicon-m-paper-airplane" class="w-5 h-5 text-white" />
                                <span>Review & Submit Requisition</span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= 5. CONFIRMATION MODAL SCREEN ================= --}}
    @if ($showConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             role="dialog"
             aria-modal="true">
            <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-5 animate-in fade-in duration-200">
                {{-- Header --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl {{ $is_urgent ? 'bg-red-100 text-red-700' : 'bg-teal-100 text-teal-800' }} flex items-center justify-center shrink-0">
                            @if ($is_urgent)
                                <x-filament::icon icon="heroicon-m-bolt" class="w-6 h-6 animate-pulse text-red-600" />
                            @else
                                <x-filament::icon icon="heroicon-m-check-badge" class="w-6 h-6 text-teal-700" />
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">
                                {{ $is_urgent ? 'Confirm STAT Emergency Requisition' : 'Confirm Ward Requisition' }}
                            </h3>
                            <p class="text-xs text-slate-500">
                                Roxas Memorial Provincial Hospital • {{ $user->unit?->name ?? 'Ward' }}
                            </p>
                        </div>
                    </div>
                    <button type="button"
                            wire:click="closeConfirm"
                            class="min-w-[48px] min-h-[48px] flex items-center justify-center text-slate-400 hover:text-slate-600 transition -mr-2 -mt-2">
                        <x-filament::icon icon="heroicon-m-x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Urgency Badge / Reason if STAT --}}
                @if ($is_urgent)
                    <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 space-y-1">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-red-800 flex items-center gap-1">
                            <x-filament::icon icon="heroicon-m-bolt" class="w-3.5 h-3.5 text-red-600" />
                            Priority: STAT Immediate Emergency
                        </span>
                        <p class="text-xs text-red-950 font-medium">
                            Clinical Reason: {{ $urgent_reason }}
                        </p>
                    </div>
                @else
                    <div class="p-3.5 rounded-xl bg-teal-50 border border-teal-200/60">
                        <span class="text-xs font-semibold text-teal-900 flex items-center gap-1.5">
                            <x-filament::icon icon="heroicon-m-calendar" class="w-4 h-4 text-teal-700" />
                            Standard Schedule Window Requisition
                        </span>
                    </div>
                @endif

                {{-- Items Table in Modal --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Requisition Lines ({{ $distinctItemsCount }} items, {{ $totalUnits }} total units)
                    </label>
                    <div class="max-h-56 overflow-y-auto rounded-xl border border-slate-200 divide-y divide-slate-100">
                        @foreach ($cartItems as $row)
                            <div class="p-3 flex items-center justify-between text-xs hover:bg-slate-50">
                                <div>
                                    <strong class="text-slate-800">{{ $row['item']->description }}</strong>
                                    <span class="text-slate-400 text-[11px] block font-mono">{{ $row['item']->code }}</span>
                                </div>
                                <span class="font-extrabold tabular-nums text-teal-800 bg-teal-50 px-2.5 py-1 rounded-lg">
                                    {{ $row['quantity'] }} {{ $row['item']->unit_of_measure }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if (filled($notes))
                    <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <strong class="text-slate-700 font-semibold block">Notes for Central Storage:</strong>
                        {{ $notes }}
                    </div>
                @endif

                <p class="text-[11px] text-slate-500">
                    By submitting, this order will be recorded in the system and dispatched to central warehouse custodians for FEFO batch allocation.
                </p>

                {{-- Action Buttons (48px Touch Targets) --}}
                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button"
                            wire:click="closeConfirm"
                            class="min-h-[48px] px-5 py-2.5 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition flex items-center justify-center">
                        Back to Edit
                    </button>
                    <button type="button"
                            wire:click="submitRequisition"
                            class="min-h-[48px] px-6 py-2.5 rounded-xl text-xs font-bold text-white transition flex items-center justify-center gap-2 shadow-xs {{ $is_urgent ? 'bg-red-600 hover:bg-red-700' : 'bg-teal-700 hover:bg-teal-800' }}">
                        <x-filament::icon icon="heroicon-m-check" class="w-4 h-4 text-white" />
                        <span>Confirm & Dispatch to Depot</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
