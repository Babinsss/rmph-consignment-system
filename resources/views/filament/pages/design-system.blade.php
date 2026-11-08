<x-filament-panels::page>
    <div class="space-y-8">
        {{-- 1. System Header --}}
        <div class="p-6 rounded-xl bg-teal-50 border border-teal-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-teal-700 text-white mb-2">
                        HECS Design System v1.0
                    </div>
                    <h1 class="text-2xl font-extrabold text-teal-950 tracking-tight">
                        Roxas Memorial Provincial Hospital • Design Tokens & Visual Language
                    </h1>
                    <p class="text-sm text-teal-800 mt-1 max-w-3xl">
                        Clean, calm, clinical hospital design system. Built on an 8px spacing grid, Inter typography, 48px minimum touch targets, and strict dual-coded accessibility (Word + Real SVG Icon + Color, zero emojis).
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-teal-200 text-teal-900 shadow-xs">
                        <x-filament::icon icon="heroicon-m-check-badge" class="w-4 h-4 text-emerald-600" />
                        WCAG 2.2 AA Compliant
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-teal-200 text-teal-900 shadow-xs">
                        <x-filament::icon icon="heroicon-m-device-phone-mobile" class="w-4 h-4 text-teal-700" />
                        Tablet Touch Ready (48px)
                    </span>
                </div>
            </div>
        </div>

        {{-- 2. Color Tokens & Semantic Palettes --}}
        <div class="p-6 rounded-xl border border-gray-200 bg-white shadow-xs space-y-6">
            <div>
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-swatch" class="w-5 h-5 text-teal-700" />
                    Color Palette Tokens
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Primary hospital brand color is Teal 700 (#0F766E). All alert states use soft tinted backgrounds with high-contrast text.
                </p>
            </div>

            {{-- Primary Teal Scale --}}
            <div class="space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-700">Primary Brand: Teal 700 (#0F766E)</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-11 gap-2 text-center text-xs">
                    <div class="p-3 rounded-lg bg-teal-50 border border-teal-100 text-teal-950 font-mono">
                        <div class="font-bold">50</div>
                        <div class="text-[10px] text-gray-500">#F0FDFA</div>
                    </div>
                    <div class="p-3 rounded-lg bg-teal-100 border border-teal-200 text-teal-950 font-mono">
                        <div class="font-bold">100</div>
                        <div class="text-[10px] text-gray-500">#CCFBF1</div>
                    </div>
                    <div class="p-3 rounded-lg bg-teal-200 text-teal-950 font-mono">
                        <div class="font-bold">200</div>
                        <div class="text-[10px] text-gray-600">#99F6E4</div>
                    </div>
                    <div class="p-3 rounded-lg bg-teal-300 text-teal-950 font-mono">
                        <div class="font-bold">300</div>
                        <div class="text-[10px] text-gray-700">#5EEAD4</div>
                    </div>
                    <div class="p-3 rounded-lg bg-teal-400 text-teal-950 font-mono">
                        <div class="font-bold">400</div>
                        <div class="text-[10px] text-gray-800">#2DD4BF</div>
                    </div>
                    <div class="p-3 rounded-lg bg-teal-500 text-white font-mono">
                        <div class="font-bold">500</div>
                        <div class="text-[10px] text-teal-100">#14B8A6</div>
                    </div>
                    <div class="p-3 rounded-lg bg-teal-600 text-white font-mono">
                        <div class="font-bold">600</div>
                        <div class="text-[10px] text-teal-100">#0D9488</div>
                    </div>
                    <div class="p-3 rounded-lg bg-teal-700 text-white font-mono ring-2 ring-teal-900 ring-offset-2">
                        <div class="font-bold">700 ★</div>
                        <div class="text-[10px] text-teal-100">#0F766E</div>
                    </div>
                    <div class="p-3 rounded-lg bg-teal-800 text-white font-mono">
                        <div class="font-bold">800</div>
                        <div class="text-[10px] text-teal-200">#115E59</div>
                    </div>
                    <div class="p-3 rounded-lg bg-teal-900 text-white font-mono">
                        <div class="font-bold">900</div>
                        <div class="text-[10px] text-teal-300">#134E4A</div>
                    </div>
                    <div class="p-3 rounded-lg bg-teal-950 text-white font-mono">
                        <div class="font-bold">950</div>
                        <div class="text-[10px] text-teal-400">#042F2E</div>
                    </div>
                </div>
            </div>

            {{-- Semantic Colors with Soft Backgrounds --}}
            <div class="space-y-2 pt-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-700">Semantic Alert Accents (Soft Backgrounds + Dark Text)</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Danger / STAT / Expired --}}
                    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm">Danger / STAT / Expired</span>
                            <x-filament::icon icon="heroicon-m-x-circle" class="w-4 h-4 text-red-600" />
                        </div>
                        <p class="text-xs text-red-700">Text: Red 700 (#B91C1C)</p>
                        <p class="text-xs text-red-700">Bg: Red 50 (#FEF2F2)</p>
                        <p class="text-xs text-red-700">Border: Red 200 (#FECACA)</p>
                    </div>

                    {{-- Warning / Expiring Soon / Pending --}}
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm">Warning / Near-Expiry / Pending</span>
                            <x-filament::icon icon="heroicon-m-exclamation-triangle" class="w-4 h-4 text-amber-600" />
                        </div>
                        <p class="text-xs text-amber-700">Text: Amber 700 (#B45309)</p>
                        <p class="text-xs text-amber-700">Bg: Amber 50 (#FFFBEB)</p>
                        <p class="text-xs text-amber-700">Border: Amber 200 (#FDE68A)</p>
                    </div>

                    {{-- Success / Fulfilled / Stock OK --}}
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm">Success / Fulfilled / In-Stock</span>
                            <x-filament::icon icon="heroicon-m-check-badge" class="w-4 h-4 text-emerald-600" />
                        </div>
                        <p class="text-xs text-emerald-700">Text: Green 700 (#15803D)</p>
                        <p class="text-xs text-emerald-700">Bg: Green 50 (#F0FDF4)</p>
                        <p class="text-xs text-emerald-700">Border: Green 200 (#BBF7D0)</p>
                    </div>

                    {{-- Info / Partially Fulfilled --}}
                    <div class="p-4 rounded-xl bg-sky-50 border border-sky-200 text-sky-900 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-sm">Info / Partially Fulfilled</span>
                            <x-filament::icon icon="heroicon-m-arrow-path" class="w-4 h-4 text-sky-600" />
                        </div>
                        <p class="text-xs text-sky-700">Text: Sky 700 (#0369A1)</p>
                        <p class="text-xs text-sky-700">Bg: Sky 50 (#F0F9FF)</p>
                        <p class="text-xs text-sky-700">Border: Sky 200 (#BAE6FD)</p>
                    </div>
                </div>
            </div>

            {{-- Neutrals: Slate Scale --}}
            <div class="space-y-2 pt-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-700">Neutrals: Slate Scale</span>
                <div class="grid grid-cols-2 sm:grid-cols-5 lg:grid-cols-10 gap-2 text-center text-xs">
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-800 font-mono">50</div>
                    <div class="p-2.5 rounded-lg bg-slate-100 border border-slate-200 text-slate-800 font-mono">100</div>
                    <div class="p-2.5 rounded-lg bg-slate-200 text-slate-800 font-mono">200</div>
                    <div class="p-2.5 rounded-lg bg-slate-300 text-slate-800 font-mono">300</div>
                    <div class="p-2.5 rounded-lg bg-slate-400 text-slate-900 font-mono">400</div>
                    <div class="p-2.5 rounded-lg bg-slate-500 text-white font-mono">500</div>
                    <div class="p-2.5 rounded-lg bg-slate-600 text-white font-mono">600</div>
                    <div class="p-2.5 rounded-lg bg-slate-700 text-white font-mono">700</div>
                    <div class="p-2.5 rounded-lg bg-slate-800 text-white font-mono">800</div>
                    <div class="p-2.5 rounded-lg bg-slate-900 text-white font-mono">900</div>
                </div>
            </div>
        </div>

        {{-- 3. Unified Badge System (Word + Real SVG Icon + Color, ZERO Emoji) --}}
        <div class="p-6 rounded-xl border border-gray-200 bg-white shadow-xs space-y-6">
            <div>
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-tag" class="w-5 h-5 text-teal-700" />
                    Unified Badge System (Word + Real SVG Icon + Color)
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Accessible for color-blind users. Strict rule: real SVG Heroicons only, <strong>zero emojis</strong>.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Category A: Request Status --}}
                <div class="p-5 rounded-xl border border-gray-100 bg-gray-50 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">1. Requisition Status</h3>
                    <div class="flex flex-col gap-2">
                        <span class="hecs-badge hecs-badge--pending self-start">
                            <x-filament::icon icon="heroicon-m-clock" />
                            Pending
                        </span>
                        <span class="hecs-badge hecs-badge--partially-fulfilled self-start">
                            <x-filament::icon icon="heroicon-m-arrow-path" />
                            Partly Fulfilled
                        </span>
                        <span class="hecs-badge hecs-badge--fulfilled self-start">
                            <x-filament::icon icon="heroicon-m-check-badge" />
                            Fulfilled
                        </span>
                        <span class="hecs-badge hecs-badge--rejected self-start">
                            <x-filament::icon icon="heroicon-m-x-circle" />
                            Rejected
                        </span>
                    </div>
                </div>

                {{-- Category B: Expiry Status --}}
                <div class="p-5 rounded-xl border border-gray-100 bg-gray-50 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">2. Batch Expiry Status</h3>
                    <div class="flex flex-col gap-2">
                        <span class="hecs-badge hecs-badge--expired self-start">
                            <x-filament::icon icon="heroicon-m-x-circle" />
                            Expired
                        </span>
                        <span class="hecs-badge hecs-badge--expiring-soon self-start">
                            <x-filament::icon icon="heroicon-m-exclamation-triangle" />
                            Expiring Soon (&lt; 90d)
                        </span>
                        <span class="hecs-badge hecs-badge--expiry-ok self-start">
                            <x-filament::icon icon="heroicon-m-check-circle" />
                            Good (&gt; 90d)
                        </span>
                    </div>
                </div>

                {{-- Category C: Stock Level & Priority --}}
                <div class="p-5 rounded-xl border border-gray-100 bg-gray-50 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">3. Stock Level & Urgency</h3>
                    <div class="flex flex-col gap-2">
                        <span class="hecs-badge hecs-badge--stock-low self-start">
                            <x-filament::icon icon="heroicon-m-exclamation-triangle" />
                            Below Min (Low)
                        </span>
                        <span class="hecs-badge hecs-badge--stock-ok self-start">
                            <x-filament::icon icon="heroicon-m-check-circle" />
                            Stock OK
                        </span>
                        <span class="hecs-badge hecs-badge--stat self-start">
                            <x-filament::icon icon="heroicon-m-bolt" />
                            STAT (Urgent)
                        </span>
                        <span class="hecs-badge hecs-badge--regular self-start">
                            <x-filament::icon icon="heroicon-m-calendar" />
                            Regular
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Spatial Grid, Border Radii & 48px Ward Touch Targets --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Spacing Grid & Radius --}}
            <div class="p-6 rounded-xl border border-gray-200 bg-white shadow-xs space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-view-columns" class="w-5 h-5 text-teal-700" />
                    8px Spacing Grid & Border Radius (8-12px)
                </h2>
                <p class="text-xs text-gray-500">
                    Strict 8px increment system (8, 16, 24, 32, 40, 48px). All buttons & inputs use 8px (`rounded-lg`), while cards & sections use 12px (`rounded-xl`).
                </p>

                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-3">
                        <div class="w-16 text-xs font-mono text-gray-500">8px (1x)</div>
                        <div class="h-6 w-8 bg-teal-100 border border-teal-300 rounded-md"></div>
                        <span class="text-xs text-gray-600 font-mono">gap-2, p-2 (micro spacing)</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-16 text-xs font-mono text-gray-500">16px (2x)</div>
                        <div class="h-6 w-16 bg-teal-200 border border-teal-400 rounded-md"></div>
                        <span class="text-xs text-gray-600 font-mono">gap-4, p-4 (default component padding)</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-16 text-xs font-mono text-gray-500">24px (3x)</div>
                        <div class="h-6 w-24 bg-teal-300 border border-teal-500 rounded-md"></div>
                        <span class="text-xs text-gray-600 font-mono">gap-6, p-6 (card/section padding)</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-16 text-xs font-mono text-gray-500">32px (4x)</div>
                        <div class="h-6 w-32 bg-teal-400 border border-teal-600 rounded-md"></div>
                        <span class="text-xs text-gray-600 font-mono">gap-8, p-8 (layout rhythm)</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-16 text-xs font-mono text-gray-500">48px (6x)</div>
                        <div class="h-6 w-48 bg-teal-700 text-white font-mono rounded-md text-[10px] flex items-center justify-center">48px Touch</div>
                        <span class="text-xs text-gray-600 font-mono">48px minimum touch target</span>
                    </div>
                </div>
            </div>

            {{-- 48px Minimum Tap Target Demonstration --}}
            <div class="p-6 rounded-xl border border-gray-200 bg-white shadow-xs space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-cursor-arrow-rays" class="w-5 h-5 text-teal-700" />
                    48px Minimum Tap Targets (Ward Tablets & Gloved Hands)
                </h2>
                <p class="text-xs text-gray-500">
                    Clinical staff wearing medical gloves on bedside tablets cannot mis-tap or miss buttons.
                </p>

                <div class="space-y-4 pt-2">
                    <div>
                        <span class="text-xs font-semibold text-gray-700 block mb-1.5">Ward Action Button (48px Height)</span>
                        <button type="button" class="ward-touch-btn px-6 rounded-xl bg-teal-700 text-white font-bold text-sm shadow-sm hover:bg-teal-800 active:scale-[0.98] gap-2">
                            <x-filament::icon icon="heroicon-m-plus-circle" class="w-5 h-5" />
                            Submit Requisition (48px Target)
                        </button>
                    </div>

                    <div>
                        <span class="text-xs font-semibold text-gray-700 block mb-1.5">Glove-Friendly Stepper (+ / -)</span>
                        <div class="inline-flex items-center border-2 border-gray-300 rounded-xl bg-white shadow-xs overflow-hidden">
                            <button type="button" class="w-12 h-12 flex items-center justify-center text-lg font-bold text-gray-700 hover:bg-gray-100 active:bg-gray-200 border-r border-gray-200">
                                −
                            </button>
                            <input type="text" readonly value="25 boxes" class="w-24 h-12 text-center font-bold text-sm border-0 focus:ring-0 text-gray-900 bg-transparent" />
                            <button type="button" class="w-12 h-12 flex items-center justify-center text-lg font-bold text-teal-700 hover:bg-teal-50 active:bg-teal-100 border-l border-gray-200">
                                +
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Sample Live Component Preview --}}
        <div class="p-6 rounded-xl border border-gray-200 bg-white shadow-xs space-y-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-clipboard-document-check" class="w-5 h-5 text-teal-700" />
                Sample Hospital Inventory Row in Design System
            </h2>
            <p class="text-xs text-gray-500">
                How tokens, typography, and badges harmonize together in real hospital tables.
            </p>

            <div class="overflow-x-auto border border-gray-200 rounded-xl">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-gray-200 text-slate-600 font-bold uppercase text-[11px]">
                            <th class="py-3 px-4">Item Code</th>
                            <th class="py-3 px-4">Item Description</th>
                            <th class="py-3 px-4">Supplier</th>
                            <th class="py-3 px-4">Batch / Expiry</th>
                            <th class="py-3 px-4">Stock on Hand</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-teal-800">SUP-001</td>
                            <td class="py-3.5 px-4 font-medium text-gray-900">Sterile Disposable Syringe 3ml with needle (box of 100)</td>
                            <td class="py-3.5 px-4 text-gray-600">Zuellig Pharma Corporation</td>
                            <td class="py-3.5 px-4">
                                <span class="hecs-badge hecs-badge--expiring-soon">
                                    <x-filament::icon icon="heroicon-m-exclamation-triangle" />
                                    Exp: Jun 2027 (82d)
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="hecs-badge hecs-badge--stock-ok">
                                    <x-filament::icon icon="heroicon-m-check-circle" />
                                    450 units
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <button class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200">
                                    View Batch
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-teal-800">DRG-004</td>
                            <td class="py-3.5 px-4 font-medium text-gray-900">Paracetamol 500mg IV Infusion 100ml bottle</td>
                            <td class="py-3.5 px-4 text-gray-600">Metro Drug Inc.</td>
                            <td class="py-3.5 px-4">
                                <span class="hecs-badge hecs-badge--expiry-ok">
                                    <x-filament::icon icon="heroicon-m-check-circle" />
                                    Exp: Dec 2028 (OK)
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="hecs-badge hecs-badge--stock-low">
                                    <x-filament::icon icon="heroicon-m-exclamation-triangle" />
                                    Below Min (12 / 50)
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <button class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200">
                                    View Batch
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
