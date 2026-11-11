@php
    $urgentRequests = $this->getPendingUrgentRequests();
@endphp

@if ($urgentRequests->isNotEmpty())
    <div class="p-4 rounded-xl border border-red-300 bg-red-50 text-red-950 shadow-sm mb-6">
        <div class="flex items-center justify-between pb-3 border-b border-red-200">
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping motion-reduce:hidden absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                </span>
                <h3 class="font-bold text-sm tracking-tight text-red-900">
                    URGENT (STAT) REQUISITION ALERT: {{ $urgentRequests->count() }} Emergency Request(s) Pending
                </h3>
            </div>
            <span class="text-xs font-semibold uppercase tracking-wider px-2 py-0.5 rounded bg-red-200 text-red-800">
                Target: &lt; 15 Minutes
            </span>
        </div>

        <div class="divide-y divide-red-200/60 mt-2">
            @foreach ($urgentRequests as $req)
                <div class="py-2.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="font-bold font-mono text-red-800">{{ $req->request_number }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[11px] font-semibold bg-red-100 text-red-800">
                                {{ $req->unit?->name }}
                            </span>
                            <span class="text-slate-500">
                                Requested by {{ $req->requestedBy?->name }} • {{ $req->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-red-900/90 italic">
                            &ldquo;{{ $req->urgent_reason }}&rdquo;
                        </p>
                        <div class="text-slate-600">
                            Requested Items:
                            @foreach ($req->lines as $line)
                                <span class="font-medium text-slate-800">
                                    {{ $line->quantity_requested }}x {{ $line->item?->description }}@if(! $loop->last), @endif
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ url('/admin/requests/' . $req->id) }}"
                       class="min-h-[44px] inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition shrink-0 shadow-xs focus-visible:outline-2 focus-visible:outline-red-600">
                        <span>Review STAT Requisition</span>
                        <x-filament::icon icon="heroicon-m-arrow-right" class="w-4 h-4 text-white" />
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif
