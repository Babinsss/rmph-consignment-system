<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consignment Requisition | RMPH Department Portal</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/rmph.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/rmph.jpg') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Instrument Sans Font & Tailored Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { 
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; 
            background-color: #f8fafc; 
            color: #0f172a; 
            overflow-x: hidden; 
        }
        .text-secondary {
            color: #475569 !important;
        }
        .checkout-container { 
            max-width: 1200px; 
            margin: 2rem auto 4rem; 
            padding-bottom: 90px; 
        }
        .portal-panel { 
            background: #ffffff; 
            border-radius: 1.25rem; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05); 
            padding: 1.75rem; 
        }
        .input-modern { 
            border-radius: 0.75rem; 
            border: 1.5px solid #cbd5e1; 
            background-color: #ffffff; 
            padding: 0.75rem 1rem; 
            font-size: 0.9375rem;
            color: #0f172a;
            font-weight: 500; 
            width: 100%; 
            transition: border-color 0.15s, box-shadow 0.15s; 
        }
        .input-modern:focus { 
            background-color: #ffffff; 
            border-color: #0f766e; 
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15); 
            outline: none; 
        }
        .input-modern::placeholder {
            color: #64748b;
        }
        .btn-modern { 
            border-radius: 0.75rem; 
            font-weight: 700; 
            padding: 0.75rem 1.25rem; 
            transition: all 0.15s; 
            min-height: 44px;
        }
        .btn-teal {
            background-color: #0f766e;
            color: #ffffff;
            border: none;
        }
        .btn-teal:hover {
            background-color: #115e59;
            color: #ffffff;
        }
        .btn-teal:focus {
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.25);
        }
        
        /* Floating Cart Button */
        .fab-cart { 
            position: fixed; 
            bottom: 2rem; 
            right: 2rem; 
            z-index: 1040; 
            border-radius: 50px; 
            padding: 0.875rem 1.75rem; 
            font-size: 1rem; 
            box-shadow: 0 8px 25px rgba(15, 118, 110, 0.35); 
            transition: transform 0.15s, box-shadow 0.15s; 
            min-height: 48px;
        }
        .fab-cart:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 12px 30px rgba(15, 118, 110, 0.45);
        }
        
        /* Pill Styling */
        .filter-pill { 
            cursor: pointer; 
            transition: all 0.15s; 
            font-weight: 600; 
            font-size: 0.8125rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
        }
        .filter-pill.active { 
            background-color: #0f766e !important; 
            color: #ffffff !important; 
            border-color: #0f766e !important;
        }
        
        /* Custom scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Stepper buttons */
        .qty-stepper-btn {
            width: 38px;
            height: 38px;
            border-radius: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.15s;
        }
        .qty-stepper-btn:hover {
            background-color: #e2e8f0;
        }

        @keyframes pulse-warning {
            0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(220, 38, 38, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
        }
        .pulse-warning { 
            animation: pulse-warning 2s infinite; 
        }
    </style>
</head>
<body>

    {{-- Toast Notification Container --}}
    <div class="toast-container position-fixed top-0 end-0 p-4" style="z-index: 1055;">
        <div id="liveToast" class="toast align-items-center text-white border-0 shadow-lg rounded-4" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex px-2 py-1">
                <div class="toast-body fw-bold fs-6" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    {{-- Clean Hospital Navbar --}}
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-3 px-3">
            <div class="container max-w-7xl d-flex justify-content-between align-items-center">
                <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                    <img src="{{ asset('images/capiz.jpg') }}" alt="Capiz Provincial Government" style="height: 38px; border-radius: 50%;">
                    <img src="{{ asset('images/rmph.jpg') }}" alt="Roxas Memorial Provincial Hospital" style="height: 38px; border-radius: 50%;">
                    <div class="ms-1 d-none d-sm-block">
                        <div class="fw-bold fs-6 text-dark lh-1">ROXAS MEMORIAL PROVINCIAL HOSPITAL</div>
                        <div class="text-secondary small font-medium" style="letter-spacing: 0.3px;">Hospital Extensive Consignment System (HECS)</div>
                    </div>
                </a>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-emerald-50 text-emerald-900 border border-emerald-300 px-3 py-1.5 rounded-pill d-none d-md-inline-flex align-items-center" style="background-color: #ecfdf5; color: #065f46; font-size: 0.75rem;">
                        <span class="spinner-grow spinner-grow-sm me-1.5" style="width: 8px; height: 8px; color: #059669;"></span> Live FEFO Allocation
                    </span>
                    <a href="/admin/login" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill px-3 py-1.5 shadow-none" style="min-height: 38px;">
                        <i class="bi bi-shield-lock-fill me-1" style="color: #0f766e;"></i> Staff Login &rarr;
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <form action="{{ route('portal.submit') }}" method="POST" id="requisitionForm" onsubmit="return validateCart()">
            @csrf
            <div class="container checkout-container">
                
                {{-- Centered RMPH Supply Brand Header --}}
                <div class="text-center mb-5 pt-2">
                    <img src="{{ asset('images/supply-logo2.png') }}" alt="RMPH Consignment System" style="height: 96px; width: auto;" class="mb-3">
                    <h2 class="fw-bold tracking-tight mb-1 text-slate-900 fs-3">Department Consignment Requisition</h2>
                    <p class="text-secondary fw-medium fs-6 mb-0">Roxas Memorial Provincial Hospital &bull; Clinical Ward Materials Request</p>
                </div>

                {{-- DYNAMIC SCHEDULING BANNERS --}}
                @if($portalMode === 'NORMAL')
                    <div class="alert alert-info border border-info-subtle text-dark text-center rounded-4 p-3 mb-4 shadow-sm fw-medium">
                        <i class="bi bi-clock-history me-2 text-primary"></i> <strong>Standard Schedule</strong>: Monday & Wednesday 8:00 AM &ndash; 5:00 PM &bull; Requisitions are routed for FEFO batch deduction.
                    </div>
                @elseif($portalMode === 'CLOSED_WEEKEND' || $portalMode === 'CLOSED_OFF_HOURS')
                    <div id="closedBanner" class="alert alert-danger border border-danger-subtle text-center rounded-4 p-4 mb-4 shadow-sm">
                        <i class="bi bi-shield-lock-fill fs-2 d-block mb-2 text-danger"></i>
                        <h4 class="fw-bold text-danger fs-5">SYSTEM CLOSED (STANDARD HOURS: MON/WED 8:00 AM &ndash; 5:00 PM)</h4>
                        <p class="mb-3 text-slate-800 fw-medium small">
                            Standard consignment requesting is currently outside operating hours. If this is a life-safety clinical emergency, activate the Emergency Override below.
                        </p>
                        <button type="button" class="btn btn-danger btn-modern pulse-warning px-4 shadow-sm" onclick="triggerBreakGlass()">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Activate Emergency Override
                        </button>
                    </div>
                @else
                    <div class="alert alert-warning border border-warning-subtle text-dark text-center rounded-4 p-3 mb-4 shadow-sm fw-medium">
                        <i class="bi bi-exclamation-circle-fill me-2 text-warning"></i> <strong>Off-Schedule Window</strong>: Only verified life-safety emergency requisitions permitted today.
                    </div>
                @endif

                @if(session('success'))
                    <script>localStorage.removeItem('rmph_cart');</script>
                    <div class="alert alert-success border-0 rounded-4 p-4 mb-4 text-center shadow-sm" style="background-color: #ecfdf5; border: 1px solid #a7f3d0 !important;">
                        <i class="bi bi-check2-circle fs-1 text-success d-block mb-2"></i> 
                        <h4 class="fw-bold text-success mb-2 fs-5">{{ session('success') }}</h4>
                        <p class="text-slate-700 small mb-3">Requisition Slip Reference: <strong>{{ session('request_number') }}</strong></p>
                        @if(session('stock_request_id'))
                            <a href="{{ route('requisition.print', session('stock_request_id')) }}" target="_blank" class="btn btn-teal btn-modern shadow-sm px-4">
                                <i class="bi bi-printer-fill me-2"></i> Print Official Requisition Slip (RIS)
                            </a>
                        @endif
                    </div>
                @endif

                @if(session('danger'))
                    <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 text-center shadow-sm">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('danger') }}
                    </div>
                @endif

                {{-- HIDDEN FLAG FOR BACKEND --}}
                <input type="hidden" name="is_emergency" id="isEmergencyFlag" value="{{ $portalMode !== 'NORMAL' ? '1' : '0' }}">

                {{-- THE MAIN PORTAL UI --}}
                <div class="row g-4 mb-4" id="mainPortalUI" style="{{ ($portalMode === 'CLOSED_WEEKEND' || $portalMode === 'CLOSED_OFF_HOURS') ? 'display: none;' : '' }}">
                    
                    {{-- Left Column: Requesting Department Details --}}
                    <div class="col-lg-5">
                        <div class="portal-panel h-100">
                            <h3 class="h6 fw-bold border-bottom pb-3 mb-4 text-uppercase text-secondary tracking-wide d-flex items-center gap-1">
                                <i class="bi bi-hospital me-1" style="color: #0f766e;"></i> 1. Authorized Requesting Unit
                            </h3>

                            <div class="mb-3">
                                <label for="departmentSelect" class="form-label text-slate-700 small fw-bold text-uppercase">Authorized Department / Unit <span class="text-danger">*</span></label>
                                <select class="form-select input-modern mb-3" name="department_name" id="departmentSelect" required onchange="updateRequestor()">
                                    <option value="" disabled selected>Select Authorized Department...</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept['name'] }}" data-head="{{ $dept['head_name'] }}">{{ $dept['label'] }}</option>
                                    @endforeach
                                </select>

                                <label for="requestedByInput" class="form-label text-slate-700 small fw-bold text-uppercase">Requisitioned By (Unit In-Charge)</label>
                                <input type="text" class="input-modern bg-slate-50 text-dark fw-bold mb-3" name="requested_by" id="requestedByInput" placeholder="Auto-filled or enter staff name" required>

                                <label for="patientMrnInput" class="form-label text-slate-700 small fw-bold text-uppercase">Patient MRN / Hospital Number (Optional)</label>
                                <input type="text" class="input-modern mb-3" id="patientMrnInput" name="patient_mrn" placeholder="e.g. MRN-2026-9041 (if patient specific)">

                                <label for="purposeTextarea" class="form-label text-slate-700 small fw-bold text-uppercase">
                                    Purpose / Clinical Remarks <span class="text-danger">*</span>
                                </label>
                                <textarea class="input-modern" id="purposeTextarea" name="purpose" rows="3" placeholder="State clinical reason (e.g. CSR stock replenishment, emergency surgery, inpatient hemodialysis)" required></textarea>
                            </div>

                            <div class="pt-3 border-top text-slate-600 small mt-4 d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle-fill shrink-0 mt-0.5" style="color: #0f766e;"></i>
                                <span>Requisitions are received by <strong>Aliana Marie Dula</strong> (Medical Supplies) &amp; <strong>Kristine Mae Batan</strong> (Pharmacy) for FEFO release.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Live Consignment Catalog & Search --}}
                    <div class="col-lg-7">
                        <div class="portal-panel h-100 d-flex flex-column">
                            <h3 class="h6 fw-bold border-bottom pb-3 mb-4 text-uppercase text-secondary tracking-wide d-flex items-center gap-1">
                                <i class="bi bi-box-seam me-1" style="color: #0f766e;"></i> 2. Consignment Catalog Directory
                            </h3>
                            
                            <div class="mb-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 ps-3 text-secondary"><i class="bi bi-search"></i></span>
                                    <input type="text" id="searchInput" class="form-control input-modern border-start-0 rounded-end-3 ps-0" placeholder="Search medicines, surgical supplies, or code..." onkeyup="filterDirectory()">
                                </div>
                                
                                {{-- Stream Filter Pills --}}
                                <div class="d-flex gap-2 overflow-auto pb-2" id="streamPills">
                                    <button type="button" class="btn btn-sm filter-pill active" onclick="filterByStream('All', this)">All Items</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary filter-pill" onclick="filterByStream('medical_supplies', this)">Medical Supplies</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary filter-pill" onclick="filterByStream('pharmacy', this)">Pharmacy Stocks</button>
                                </div>
                            </div>

                            <div style="flex-grow: 1; overflow-y: auto; max-height: 520px;" class="pe-2" id="directoryContainer">
                                <ul class="list-unstyled mb-0 divide-y divide-slate-100" id="directoryList">
                                    @foreach($items as $item)
                                        <li class="py-3.5 border-bottom border-slate-100 directory-item d-flex flex-column sm:flex-row justify-content-between align-items-start sm:align-items-center gap-3" 
                                            data-name="{{ strtolower($item->name . ' ' . $item->generic_name . ' ' . $item->item_code) }}" 
                                            data-stream="{{ $item->department }}"
                                            data-category="{{ $item->category }}">
                                            
                                            <div style="max-width: 65%;">
                                                <div class="fw-bold text-slate-900 mb-0.5" style="font-size: 0.9375rem;">{{ $item->name }}</div>
                                                @if($item->generic_name)
                                                    <div class="text-secondary text-uppercase mb-1 fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.3px;">{{ $item->generic_name }}</div>
                                                @endif
                                                <div class="d-flex flex-wrap align-items-center gap-2" style="font-size: 0.8125rem;">
                                                    <span class="badge text-white px-2 py-1 rounded-pill" style="background-color: {{ $item->department === 'pharmacy' ? '#1d4ed8' : '#0f766e' }}; font-size: 0.75rem;">
                                                        {{ $item->department === 'pharmacy' ? 'Pharmacy' : 'Med Supplies' }}
                                                    </span>
                                                    <span class="text-secondary fw-medium">&bull; {{ $item->item_code }}</span>
                                                    <span class="text-secondary fw-semibold">&bull; ₱{{ number_format($item->unit_cost, 2) }}/{{ $item->uom }}</span>
                                                </div>
                                                <div class="mt-1" style="font-size: 0.8125rem;">
                                                    <strong style="color: {{ $item->current_stock > 0 ? '#166534' : '#991b1b' }};">
                                                        {{ $item->current_stock }} {{ $item->uom }} available
                                                    </strong>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex align-items-center gap-2 w-100 sm:w-auto justify-content-end">
                                                @if($item->current_stock <= 0)
                                                    <span class="badge rounded-pill px-3 py-1.5" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; font-size: 0.75rem; font-weight: 700;">Out of Stock</span>
                                                    <button type="button" class="btn btn-sm btn-light fw-bold rounded-pill" disabled style="min-height: 40px; color: #64748b;">Unavailable</button>
                                                @else
                                                    <div class="d-flex align-items-center gap-1.5">
                                                        <button type="button" class="qty-stepper-btn" onclick="stepQty('dirQty-{{ $item->id }}', -1)" aria-label="Decrease quantity">&minus;</button>
                                                        <input type="number" id="dirQty-{{ $item->id }}" class="form-control form-control-sm text-center fw-bold border-secondary-subtle" style="width: 54px; height: 38px; border-radius: 0.5rem;" value="1" min="1" max="{{ $item->current_stock }}">
                                                        <button type="button" class="qty-stepper-btn" onclick="stepQty('dirQty-{{ $item->id }}', 1)" aria-label="Increase quantity">&plus;</button>
                                                        
                                                        <button type="button" class="btn btn-sm btn-teal fw-bold rounded-pill shadow-sm px-3 ms-1" style="min-height: 38px;" onclick="addFromDirectory('{{ $item->id }}', '{{ addslashes($item->name) }}', {{ $item->current_stock }}, '{{ $item->uom }}')">
                                                            <i class="bi bi-plus-lg me-1"></i> Add
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div id="noResultsMsg" class="text-center py-5 text-secondary small fw-medium" style="display: none;">
                                    <i class="bi bi-search fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                    No consignment items match your search.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Floating Cart Button --}}
            <button class="btn btn-teal fab-cart d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" id="fabCartButton">
                <i class="bi bi-cart3 fs-5"></i> View Requisition Cart 
                <span class="badge bg-white text-dark rounded-pill ms-1 fw-bold" id="fabCartCount" style="font-size: 0.8125rem;">0</span>
            </button>

            {{-- Offcanvas Cart Drawer --}}
            <div class="offcanvas offcanvas-end rounded-start-4 border-0 shadow" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel" style="width: 440px; max-width: 90vw;">
                <div class="offcanvas-header border-bottom p-4">
                    <h4 class="offcanvas-title fw-bold d-flex align-items-center gap-2 fs-5" id="cartOffcanvasLabel" style="color: #0f766e;">
                        <i class="bi bi-bag-check-fill"></i> Consignment Request Summary
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-4 bg-slate-50 d-flex flex-column">
                    <input type="hidden" name="cart_data" id="cartData">
                    
                    <div id="emptyCart" class="text-center py-5 text-secondary fw-medium flex-grow-1">
                        <i class="bi bi-cart-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
                        Your requisition cart is currently empty.<br>
                        <span class="small">Add items from the catalog to submit.</span>
                    </div>
                    
                    <div id="cartList" class="flex-grow-1 overflow-auto pe-1"></div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-teal btn-modern w-100 fs-6 shadow-sm">
                            <i class="bi bi-send-fill me-2"></i> Submit Requisition Slip (RIS)
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    {{-- EMERGENCY CONFIRMATION MODAL --}}
    <div class="modal fade" id="emergencyConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content portal-panel p-4 border border-danger border-2">
                <div class="modal-header border-0 pb-0">
                    <h4 class="fw-bold text-danger fs-5"><i class="bi bi-exclamation-triangle-fill me-2"></i>Emergency Schedule Override</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body rounded-3 p-3 mt-3 border border-danger-subtle" style="background-color: #fef2f2;">
                    <p class="text-dark fw-bold mb-2">WARNING: This override is heavily audited by the Capiz Provincial Government and Chief of Hospital.</p>
                    <p class="text-danger fw-bold mb-0">Confirm that this request is a critical life-safety patient emergency.</p>
                </div>
                <div class="modal-footer border-0 pt-3">
                    <button type="button" class="btn btn-light btn-modern text-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-modern shadow-sm fw-bold" onclick="executeBreakGlass()">
                        <i class="bi bi-shield-exclamation me-1"></i> Confirm Emergency Override
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        const allItems = @json($items);
        let cart = JSON.parse(localStorage.getItem('rmph_cart')) || [];
        let currentStreamFilter = 'All';

        document.addEventListener('DOMContentLoaded', () => {
            updateUI();
        });

        function stepQty(inputId, delta) {
            const el = document.getElementById(inputId);
            if (!el) return;
            const current = parseInt(el.value) || 1;
            const min = parseInt(el.min) || 1;
            const max = parseInt(el.max) || 999999;
            const next = Math.max(min, Math.min(max, current + delta));
            el.value = next;
        }

        function showToast(message, type = 'danger') {
            const toastEl = document.getElementById('liveToast');
            const toastBody = document.getElementById('toastMessage');
            
            toastEl.className = `toast align-items-center text-white bg-${type} border-0 shadow-lg rounded-4`;
            toastBody.innerHTML = message;
            
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        }

        function filterDirectory() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const items = document.querySelectorAll('.directory-item');
            let visibleCount = 0;

            items.forEach(item => {
                const name = item.getAttribute('data-name');
                const stream = item.getAttribute('data-stream');
                
                const matchesSearch = name.includes(searchTerm);
                const matchesStream = currentStreamFilter === 'All' || stream === currentStreamFilter;

                if (matchesSearch && matchesStream) {
                    item.style.setProperty('display', 'flex', 'important');
                    visibleCount++;
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });

            document.getElementById('noResultsMsg').style.display = visibleCount === 0 ? 'block' : 'none';
        }

        function filterByStream(stream, element) {
            currentStreamFilter = stream;
            
            document.querySelectorAll('#streamPills .filter-pill').forEach(pill => {
                pill.classList.remove('active');
                pill.classList.add('btn-outline-secondary');
            });
            
            element.classList.remove('btn-outline-secondary');
            element.classList.add('active');
            
            filterDirectory();
        }

        function updateRequestor() {
            const select = document.getElementById('departmentSelect');
            const headName = select.options[select.selectedIndex].getAttribute('data-head');
            document.getElementById('requestedByInput').value = headName || '';
        }

        function addFromDirectory(id, name, maxStock, uom) {
            const qtyInput = document.getElementById('dirQty-' + id);
            const qtyToAdd = parseInt(qtyInput.value) || 1;

            if (qtyToAdd < 1) {
                showToast("<i class='bi bi-info-circle-fill me-2'></i> Quantity must be at least 1.", "warning text-dark");
                return;
            }

            const existing = cart.find(i => i.id == id);
            const currentQty = existing ? parseInt(existing.qty) : 0;

            if (currentQty + qtyToAdd > maxStock) {
                showToast(`<i class="bi bi-exclamation-triangle-fill me-2"></i> Cannot request more than available live stock (${maxStock} ${uom}).`, 'danger');
                return;
            }

            if (existing) {
                existing.qty = currentQty + qtyToAdd;
            } else {
                cart.push({ id: id, name: name, qty: qtyToAdd, uom: uom });
            }
            
            updateUI();
            
            const cartOffcanvas = new bootstrap.Offcanvas(document.getElementById('cartOffcanvas'));
            cartOffcanvas.show();
            
            showToast(`<i class="bi bi-check-circle-fill me-2"></i> Added <b>${qtyToAdd} ${uom}</b> x ${name} to cart.`, 'success');
            qtyInput.value = 1;
        }

        function updateCartQty(index, delta) {
            if (!cart[index]) return;
            const newQty = cart[index].qty + delta;
            if (newQty <= 0) {
                removeItem(index);
                return;
            }
            const itemMeta = allItems.find(i => i.id == cart[index].id);
            if (itemMeta && newQty > itemMeta.current_stock) {
                showToast(`<i class="bi bi-exclamation-triangle-fill me-2"></i> Cannot exceed available stock (${itemMeta.current_stock} ${cart[index].uom}).`, 'danger');
                return;
            }
            cart[index].qty = newQty;
            updateUI();
        }

        function updateUI() {
            localStorage.setItem('rmph_cart', JSON.stringify(cart));
            document.getElementById('cartData').value = JSON.stringify(cart);
            document.getElementById('fabCartCount').innerText = cart.length;
            
            const list = document.getElementById('cartList');
            const empty = document.getElementById('emptyCart');
            
            if (cart.length > 0) {
                empty.style.display = 'none';
                list.innerHTML = '';
                cart.forEach((item, index) => {
                    list.innerHTML += `
                        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-3 shadow-sm mb-2 border border-slate-200">
                            <div style="max-width: 60%;">
                                <div class="fw-bold text-slate-900 lh-sm" style="font-size: 0.875rem;">${item.name}</div>
                                <span class="badge px-2 py-0.5 mt-1 border" style="background-color: #f0fdfa; color: #0f766e; border-color: #ccfbf1 !important; font-size: 0.75rem;">
                                    ${item.qty} ${item.uom || 'units'}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5">
                                <button type="button" class="qty-stepper-btn" style="width: 32px; height: 32px;" onclick="updateCartQty(${index}, -1)" aria-label="Decrease quantity">&minus;</button>
                                <span class="fw-bold text-dark px-1" style="font-size: 0.875rem;">${item.qty}</span>
                                <button type="button" class="qty-stepper-btn" style="width: 32px; height: 32px;" onclick="updateCartQty(${index}, 1)" aria-label="Increase quantity">&plus;</button>
                                <button type="button" class="btn btn-sm text-danger border-0 p-1 ms-1" 
                                        style="font-size: 1.1rem; line-height: 1;" 
                                        onclick="removeItem(${index})" title="Remove item">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
            } else {
                empty.style.display = 'block';
                list.innerHTML = '';
            }
        }

        function removeItem(index) {
            cart.splice(index, 1);
            updateUI();
        }

        let isCartSubmitting = false;
        function validateCart() {
            if (isCartSubmitting) {
                return false;
            }
            if (cart.length === 0) { 
                showToast(`<i class="bi bi-info-circle-fill me-2"></i> Please add at least one consignment item to submit a requisition.`, 'warning text-dark');
                return false; 
            }
            isCartSubmitting = true;
            const submitBtn = document.querySelector('#requisitionForm button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting Requisition...';
            }
            return true;
        }

        function triggerBreakGlass() {
            const modal = new bootstrap.Modal(document.getElementById('emergencyConfirmModal'));
            modal.show();
        }

        function executeBreakGlass() {
            bootstrap.Modal.getInstance(document.getElementById('emergencyConfirmModal')).hide();
            document.getElementById('closedBanner').style.display = 'none';
            document.getElementById('mainPortalUI').style.display = 'flex';
            document.getElementById('fabCartButton').style.setProperty('display', 'flex', 'important');
            document.getElementById('isEmergencyFlag').value = '1';
            showToast("<i class='bi bi-shield-exclamation me-2'></i> Emergency Override Activated. Please state clinical justification.", "danger");
        }
    </script>
</body>
</html>
