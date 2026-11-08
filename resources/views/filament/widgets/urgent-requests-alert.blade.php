@php
    $urgentRequests = $this->getPendingUrgentRequests();
@endphp

@if ($urgentRequests->isNotEmpty())
    <div class="p-4 rounded-xl border border-red-300 bg-red-50 text-red-950 dark:bg-red-950/30 dark:border-red-900/60 dark:text-red-200 shadow-sm mb-6">
        <div class="flex items-center justify-between pb-3 border-b border-red-200 dark:border-red-900/50">
            <div class="flex items-center gap-2.5">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                </span>
                <h3 class="font-bold text-sm tracking-tight text-red-900 dark:text-red-200">
                    URGENT (STAT) REQUISITION ALERT: {{ $urgentRequests->count() }} Emergency Request(s) Pending
                </h3>
            </div>
            <span class="text-xs font-semibold uppercase tracking-wider px-2 py-0.5 rounded bg-red-200 dark:bg-red-900 text-red-800 dark:text-red-200">
                Target: &lt; 15 Minutes
            </span>
        </div>

        <div class="divide-y divide-red-200/60 dark:divide-red-900/40 mt-2">
            @foreach ($urgentRequests as $req)
                <div class="py-2.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="font-bold font-mono text-red-800 dark:text-red-300">{{ $req->request_number }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[11px] font-semibold bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200">
                                {{ $req->unit?->name }}
                            </span>
                            <span class="text-gray-500 dark:text-gray-400">
                                Requested by {{ $req->requestedBy?->name }} • {{ $req->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-red-900/90 dark:text-red-300 italic">
                            &ldquo;{{ $req->urgent_reason }}&rdquo;
                        </p>
                        <div class="text-gray-600 dark:text-gray-400">
                            Requested Items:
                            @foreach ($req->lines as $line)
                                <span class="font-medium text-gray-800 dark:text-gray-200">
                                    {{ $line->quantity_requested }}x {{ $line->item?->description }}@if(! $loop->last), @endif
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ url('/admin/requests/' . $req->id) }}"
                       class="inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-600 text-white hover:bg-red-500 transition shrink-0 shadow-xs">
                        Review STAT Requisition &rarr;
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif
