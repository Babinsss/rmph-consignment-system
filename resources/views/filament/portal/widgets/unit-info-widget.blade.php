@php
    $data = $this->getUnitData();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between py-1">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200">
                        {{ $data['unitCode'] }}
                    </span>
                    <span class="text-xs text-gray-500 font-medium">
                        {{ $data['currentTime'] }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                    {{ $data['unitName'] }}
                </h1>
                <p class="text-sm text-gray-600">
                    Signed in as <strong class="text-gray-900">{{ $data['userName'] }}</strong> • Central Consignment Requisition Portal
                </p>

                {{-- Requisition Window Status Banner --}}
                <div class="pt-1">
                    @if ($data['isWindowOpen'])
                        <div class="inline-flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-medium">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-600"></span>
                            </span>
                            <span>
                                Regular Requisition Window: <strong>OPEN</strong> ({{ $data['scheduleSummary'] }})
                            </span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs font-medium">
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-600"></span>
                            <span>
                                Regular Window: <strong>CLOSED</strong> • Urgent (STAT) Requisitions Accepted 24/7
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Big Touch-Friendly Action Buttons for Ward Tablets --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2 lg:pt-0">
                <a href="{{ url('/portal/requests/create') }}"
                   class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 min-h-[48px] rounded-xl text-base font-bold bg-teal-700 text-white hover:bg-teal-800 active:scale-[0.98] transition shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                    <x-filament::icon icon="heroicon-m-plus-circle" class="w-5 h-5 shrink-0" />
                    <span>New Requisition</span>
                </a>

                <a href="{{ url('/portal/items') }}"
                   class="inline-flex items-center justify-center gap-2.5 px-5 py-3.5 min-h-[48px] rounded-xl text-base font-semibold border-2 border-gray-300 bg-white text-gray-800 hover:bg-gray-50 hover:border-gray-400 active:scale-[0.98] transition shadow-xs focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                    <x-filament::icon icon="heroicon-m-clipboard-document-list" class="w-5 h-5 text-teal-700 shrink-0" />
                    <span>Browse Catalog ({{ $data['totalItemsCount'] }})</span>
                </a>
            </div>
        </div>

        <div class="mt-5 pt-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-gray-500">
            <span>Roxas Memorial Provincial Hospital • Central Storage Depot Requisitions</span>
            <span>
                Unit Pending Requisitions: <strong class="text-teal-800 font-semibold">{{ $data['pendingRequestsCount'] }}</strong>
            </span>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
