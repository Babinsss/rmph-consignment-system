@auth
    @if (auth()->user()?->isRequestingUnit() || auth()->user()?->isAdmin())
        @php
            $currentPath = request()->path();
            $isHome = $currentPath === 'portal';
            $isCatalog = str_starts_with($currentPath, 'portal/items');
            $isRequests = str_starts_with($currentPath, 'portal/requests') && request()->query('urgent') != 1 && !str_contains(request()->fullUrl(), 'urgent=1');
            $isStat = request()->query('urgent') == 1 || str_contains(request()->fullUrl(), 'urgent=1');
        @endphp

        <nav aria-label="Ward Navigation"
             class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/60 shadow-[0_-10px_30px_-5px_rgba(0,0,0,0.03)] px-4 py-2 transition-all">
            <div class="max-w-md mx-auto grid grid-cols-4 gap-2">
                {{-- 1. Home --}}
                <a href="{{ url('/portal') }}"
                   class="min-h-[48px] flex flex-col items-center justify-center rounded-xl px-2 py-1 transition group {{ $isHome ? 'text-teal-700 font-bold bg-teal-50/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <x-filament::icon icon="heroicon-o-home" class="w-5 h-5 {{ $isHome ? 'text-teal-700' : 'text-slate-500 group-hover:text-slate-800' }}" />
                    <span class="text-[11px] mt-0.5 tracking-tight">Home</span>
                </a>

                {{-- 2. Catalog --}}
                <a href="{{ url('/portal/items') }}"
                   class="min-h-[48px] flex flex-col items-center justify-center rounded-xl px-2 py-1 transition group {{ $isCatalog ? 'text-teal-700 font-bold bg-teal-50/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <x-filament::icon icon="heroicon-o-archive-box" class="w-5 h-5 {{ $isCatalog ? 'text-teal-700' : 'text-slate-500 group-hover:text-slate-800' }}" />
                    <span class="text-[11px] mt-0.5 tracking-tight">Catalog</span>
                </a>

                {{-- 3. My Requests --}}
                <a href="{{ url('/portal/requests') }}"
                   class="min-h-[48px] flex flex-col items-center justify-center rounded-xl px-2 py-1 transition group {{ $isRequests ? 'text-teal-700 font-bold bg-teal-50/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <x-filament::icon icon="heroicon-o-inbox-stack" class="w-5 h-5 {{ $isRequests ? 'text-teal-700' : 'text-slate-500 group-hover:text-slate-800' }}" />
                    <span class="text-[11px] mt-0.5 tracking-tight">Requests</span>
                </a>

                {{-- 4. STAT Emergency Request --}}
                <a href="{{ url('/portal/requests/create?urgent=1') }}"
                   class="min-h-[48px] flex flex-col items-center justify-center rounded-xl px-2 py-1 border transition group {{ $isStat ? 'bg-red-600 text-white border-red-700 font-bold shadow-xs' : 'bg-red-50/80 text-red-700 border-red-200/80 hover:bg-red-100/80' }}">
                    <x-filament::icon icon="heroicon-m-bolt" class="w-5 h-5 {{ $isStat ? 'text-white' : 'text-red-600 animate-pulse' }}" />
                    <span class="text-[11px] mt-0.5 font-bold tracking-tight">STAT</span>
                </a>
            </div>
        </nav>

        {{-- Spacing pad so content is never obscured by the fixed bottom nav --}}
        <div class="h-20" aria-hidden="true"></div>
    @endif
@endauth
