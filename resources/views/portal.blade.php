<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consignment Requisition | RMPH Department Portal</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/rmph.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/rmph.jpg') }}">

    <!-- Bootstrap 5 CSS (Local + CDN fallback) -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f1f5f9; 
            color: #0f172a; 
            overflow-x: hidden; 
        }
        .checkout-container { 
            max-width: 1200px; 
            margin: 2.5rem auto 4rem; 
            padding-bottom: 80px; 
        }
        .bento-card { 
            background: #fff; 
            border-radius: 1.5rem; 
            border: none; 
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); 
            padding: 2rem; 
        }
        .input-modern { 
            border-radius: 1rem; 
            border: 2px solid #e2e8f0; 
            background-color: #f8fafc; 
            padding: 0.8rem 1.2rem; 
            font-weight: 500; 
            width: 100%; 
            transition: all 0.2s; 
        }
        .input-modern:focus { 
            background-color: #fff; 
            border-color: #0d9488; 
            box-shadow: 0 4px 15px rgba(13, 148, 136, 0.15); 
            outline: none; 
        }
        .btn-modern { 
            border-radius: 1rem; 
            font-weight: 700; 
            padding: 0.8rem 1.5rem; 
            transition: all 0.2s; 
        }
        .btn-modern:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 20px rgba(0,0,0,0.1); 
        }
        .btn-teal {
            background-color: #0d9488;
            color: white;
            border: none;
        }
        .btn-teal:hover {
            background-color: #0f766e;
            color: white;
        }
        
        /* Floating Cart Button */
        .fab-cart { 
            position: fixed; 
            bottom: 2rem; 
            right: 2rem; 
            z-index: 1040; 
            border-radius: 50px; 
            padding: 1rem 2rem; 
            font-size: 1.1rem; 
            box-shadow: 0 10px 25px rgba(13, 148, 136, 0.4); 
            transition: transform 0.2s; 
        }
        .fab-cart:hover { 
            transform: scale(1.05); 
        }
        
        /* Pill Styling */
        .filter-pill { 
            cursor: pointer; 
            transition: all 0.2s; 
            font-weight: 600; 
        }
        .filter-pill.active { 
            background-color: #0d9488 !important; 
            color: white !important; 
            border-color: #0d9488 !important;
        }
        
        /* Custom scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Emergency Pulse */
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
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm py-2 px-3">
        <div class="container max-w-7xl d-flex justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                <img src="{{ asset('images/capiz.jpg') }}" alt="Capiz Provincial Government" style="height: 38px; border-radius: 50%;">
                <img src="{{ asset('images/rmph.jpg') }}" alt="Roxas Memorial Provincial Hospital" style="height: 38px; border-radius: 50%;">
                <div class="ms-1 d-none d-sm-block">
                    <div class="fw-bold fs-6 text-dark lh-1">ROXAS MEMORIAL PROVINCIAL HOSPITAL</div>
                    <div class="text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">Hospital Extensive Consignment System (HECS)</div>
                </div>
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill d-none d-md-inline-flex align-items-center">
                    <span class="spinner-grow spinner-grow-sm text-success me-1.5" style="width: 8px; height: 8px;"></span> Live FEFO Inventory
                </span>
                <a href="/admin/login" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill px-3 shadow-none">
                    <i class="bi bi-shield-lock-fill me-1 text-teal"></i> Staff Login &rarr;
                </a>
            </div>
        </div>
    </nav>

    <form action="{{ route('portal.submit') }}" method="POST" id="requisitionForm" onsubmit="return validateCart()">
        @csrf
        <div class="container checkout-container">
            
            {{-- Centered RMPH Supply Brand Header --}}
            <div class="text-center mb-4 pt-2">
                <img src="{{ asset('images/supply-logo2.png') }}" alt="RMPH Consignment System" style="height: 120px; width: auto;" class="mb-3">
                <h2 class="fw-bolder tracking-tight mb-1">Consignment Requisition</h2>
                <p class="text-muted fw-medium fs-6 mb-0">Department Portal &bull; Roxas Memorial Provincial Hospital</p>
            </div>

            {{-- DYNAMIC SCHEDULING BANNERS --}}
            @if($portalMode === 'NORMAL')
                <div class="alert bg-primary bg-opacity-10 border border-primary border-opacity-25 text-primary text-center rounded-4 p-3 mb-4 shadow-sm fw-bold">
                    <i class="bi bi-clock-history me-2"></i> Normal Operations: Mon/Wed 8:00 AM - 5:00 PM &bull; Requisitions are routed for FEFO batch deduction.
                </div>
            @elseif($portalMode === 'CLOSED_WEEKEND' || $portalMode === 'CLOSED_OFF_HOURS')
                <div id="closedBanner" class="alert bg-danger bg-opacity-10 border border-danger border-opacity-50 text-danger text-center rounded-4 p-4 mb-4 shadow-sm">
                    <i class="bi bi-shield-lock-fill fs-1 d-block mb-2"></i>
                    <h5 class="fw-bolder">SYSTEM CLOSED (STANDARD HOURS: MON/WED 8:00 AM - 5:00 PM)</h5>
                    <p class="mb-3 text-dark fw-medium small">
                        Standard consignment requesting is currently outside operating hours. If this is a life-safety clinical emergency, activate the Emergency Override below.
                    </p>
                    <button type="button" class="btn btn-danger btn-modern pulse-warning px-4 shadow-sm" onclick="triggerBreakGlass()">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Activate Emergency Override
                    </button>
                </div>
            @else
                <div class="alert bg-warning bg-opacity-10 border border-warning text-dark text-center rounded-4 p-3 mb-4 shadow-sm fw-bold">
                    <i class="bi bi-exclamation-circle-fill me-2 text-warning"></i> Off-Schedule Window: Only Emergency requisitions permitted today.
                </div>
            @endif

            @if(session('success'))
                <script>localStorage.removeItem('rmph_cart');</script>
                <div class="alert alert-success border-0 rounded-4 p-4 mb-4 text-center shadow-sm">
                    <i class="bi bi-check2-circle fs-1 text-success d-block mb-2"></i> 
                    <h5 class="fw-bold text-success mb-2">{{ session('success') }}</h5>
                    <p class="text-muted small mb-3">Requisition Slip Reference: <strong>{{ session('request_number') }}</strong></p>
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
                    <div class="bento-card h-100">
                        <h6 class="fw-bolder border-bottom pb-3 mb-3 text-uppercase small text-muted">
                            <i class="bi bi-hospital me-1 text-teal"></i> 1. Authorized Requesting Unit
                        </h6>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Authorized Department / Unit <span class="text-danger">*</span></label>
                            <select class="form-select input-modern mb-3" name="department_name" id="departmentSelect" required onchange="updateRequestor()">
                                <option value="" disabled selected>Select Authorized Department...</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept['name'] }}" data-head="{{ $dept['head_name'] }}">{{ $dept['label'] }}</option>
                                @endforeach
                            </select>

                            <label class="form-label text-muted small fw-bold text-uppercase">Requisitioned By (Unit In-Charge)</label>
                            <input type="text" class="input-modern bg-light text-dark fw-bold mb-3" name="requested_by" id="requestedByInput" placeholder="Auto-filled or enter staff name" required>

                            <label class="form-label text-muted small fw-bold text-uppercase">Patient MRN / Hospital Number (Optional)</label>
                            <input type="text" class="input-modern mb-3" name="patient_mrn" placeholder="e.g. MRN-2026-9041 (if patient specific)">

                            <label class="form-label text-muted small fw-bold text-uppercase">
                                Purpose / Clinical Remarks <span class="text-danger">*</span>
                            </label>
                            <textarea class="input-modern" name="purpose" rows="3" placeholder="State reason (e.g. CSR stock replenishment, emergency surgery, inpatient hemodialysis)" required></textarea>
                        </div>

                        <div class="p-3 bg-light rounded-4 border text-muted small">
                            <i class="bi bi-info-circle-fill text-teal me-1"></i>
                            Requisitions are received by <strong>Aliana Marie Dula</strong> (Medical Supplies) & <strong>Kristine Mae Batan</strong> (Pharmacy) for FEFO release.
                        </div>
                    </div>
                </div>

                {{-- Right Column: Live Consignment Catalog & Search --}}
                <div class="col-lg-7">
                    <div class="bento-card h-100 d-flex flex-column">
                        <h6 class="fw-bolder border-bottom pb-3 mb-3 text-uppercase small text-muted">
                            <i class="bi bi-box-seam me-1 text-teal"></i> 2. Consignment Catalog Directory
                        </h6>
                        
                        <div class="mb-3">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-white border-end-0 rounded-start-4 ps-3 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchInput" class="form-control input-modern border-start-0 rounded-end-4 ps-0" placeholder="Search medicines, surgical supplies, or lot..." onkeyup="filterDirectory()">
                            </div>
                            
                            {{-- Stream Filter Pills --}}
                            <div class="d-flex gap-2 overflow-auto pb-2" id="streamPills">
                                <span class="badge bg-teal text-white filter-pill active px-3 py-2 rounded-pill" onclick="filterByStream('All', this)">All Items</span>
                                <span class="badge bg-light text-dark border filter-pill px-3 py-2 rounded-pill" onclick="filterByStream('medical_supplies', this)">Medical Supplies</span>
                                <span class="badge bg-light text-dark border filter-pill px-3 py-2 rounded-pill" onclick="filterByStream('pharmacy', this)">Pharmacy Stocks</span>
                            </div>
                        </div>

                        <div style="flex-grow: 1; overflow-y: auto; max-height: 480px;" class="pe-2" id="directoryContainer">
                            <ul class="list-unstyled mb-0" id="directoryList">
                                @foreach($items as $item)
                                    <li class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-light directory-item" 
                                        data-name="{{ strtolower($item->name . ' ' . $item->generic_name . ' ' . $item->item_code) }}" 
                                        data-stream="{{ $item->department }}"
                                        data-category="{{ $item->category }}">
                                        
                                        <div style="max-width: 65%;">
                                            <div class="small fw-bold text-dark mb-0.5">{{ $item->name }}</div>
                                            @if($item->generic_name)
                                                <div class="text-muted text-uppercase mb-1" style="font-size: 0.70rem; letter-spacing: 0.5px;">{{ $item->generic_name }}</div>
                                            @endif
                                            <div class="d-flex align-items-center gap-2" style="font-size: 0.70rem;">
                                                <span class="badge {{ $item->department === 'pharmacy' ? 'bg-primary' : 'bg-teal' }} text-white" style="background-color: {{ $item->department === 'pharmacy' ? '#2563eb' : '#0d9488' }};">
                                                    {{ $item->department === 'pharmacy' ? 'Pharmacy' : 'Med Supplies' }}
                                                </span>
                                                <span class="text-muted">&bull; {{ $item->item_code }}</span>
                                                <span class="text-muted">&bull; ₱{{ number_format($item->unit_cost, 2) }}/{{ $item->uom }}</span>
                                            </div>
                                            <div class="mt-1" style="font-size: 0.75rem;">
                                                <strong class="{{ $item->current_stock > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $item->current_stock }} {{ $item->uom }} available
                                                </strong>
                                            </div>
                                        </div>
                                        
                                        <div class="text-end d-flex flex-column align-items-end gap-1">
                                            @if($item->current_stock <= 0)
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 border border-danger border-opacity-25" style="font-size: 0.75rem;">Out of Stock</span>
                                                <button type="button" class="btn btn-sm btn-light text-muted fw-bold rounded-pill" disabled>Unavailable</button>
                                            @else
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="number" id="dirQty-{{ $item->id }}" class="form-control form-control-sm text-center fw-bold border-secondary-subtle" style="width: 65px; border-radius: 8px;" value="1" min="1" max="{{ $item->current_stock }}">
                                                    
                                                    <button type="button" class="btn btn-sm btn-teal fw-bold rounded-pill shadow-sm px-3" onclick="addFromDirectory('{{ $item->id }}', '{{ addslashes($item->name) }}', {{ $item->current_stock }}, '{{ $item->uom }}')">
                                                        <i class="bi bi-plus-lg"></i> Add
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <div id="noResultsMsg" class="text-center py-5 text-muted small fw-medium" style="display: none;">
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
            <i class="bi bi-cart3 fs-4"></i> View Requisition Cart 
            <span class="badge bg-white text-dark rounded-pill ms-1 fs-6 fw-bold" id="fabCartCount">0</span>
        </button>

        {{-- Offcanvas Cart Drawer --}}
        <div class="offcanvas offcanvas-end rounded-start-4 border-0 shadow" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel" style="width: 420px;">
            <div class="offcanvas-header border-bottom p-4">
                <h5 class="offcanvas-title fw-bolder d-flex align-items-center gap-2" id="cartOffcanvasLabel" style="color: #0f766e;">
                    <i class="bi bi-bag-check-fill"></i> Consignment Request Summary
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-4 bg-light d-flex flex-column">
                <input type="hidden" name="cart_data" id="cartData">
                
                <div id="emptyCart" class="text-center py-5 text-muted fw-medium flex-grow-1">
                    <i class="bi bi-cart-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
                    Your requisition cart is currently empty.<br>
                    <span class="small">Add items from the catalog to submit.</span>
                </div>
                
                <div id="cartList" class="flex-grow-1 overflow-auto pe-1"></div>

                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-teal btn-modern w-100 fs-5 shadow-sm">
                        <i class="bi bi-send-fill me-2"></i> Submit Requisition Slip (RIS)
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- EMERGENCY CONFIRMATION MODAL --}}
    <div class="modal fade" id="emergencyConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bento-card p-3 border-0 border border-danger border-2 bg-body">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bolder text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Emergency Schedule Override</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-danger bg-opacity-10 rounded-4 p-4 mt-3 border border-danger border-opacity-25">
                    <p class="text-dark fw-bold mb-2">WARNING: This override is heavily audited by the Capiz Provincial Government and Chief of Hospital.</p>
                    <p class="text-danger fw-bolder mb-0">Confirm that this request is a critical life-safety patient emergency.</p>
                </div>
                <div class="modal-footer border-0 pt-3">
                    <button type="button" class="btn btn-light btn-modern text-muted fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-modern shadow-sm fw-bold" onclick="executeBreakGlass()">
                        <i class="bi bi-shield-exclamation me-1"></i> Confirm Emergency Override
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS (Local + CDN fallback) -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        const allItems = @json($items);
        let cart = JSON.parse(localStorage.getItem('rmph_cart')) || [];
        let currentStreamFilter = 'All';

        document.addEventListener('DOMContentLoaded', () => {
            updateUI();
        });

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
                pill.classList.add('bg-light', 'text-dark', 'border');
            });
            
            element.classList.remove('bg-light', 'text-dark', 'border');
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
                        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm mb-3 border border-light">
                            <div style="max-width: 68%;">
                                <div class="fw-bold text-dark lh-sm" style="font-size: 0.9rem;">${item.name}</div>
                                <span class="badge bg-teal-50 text-teal-800 border px-2 py-0.5 mt-1" style="background-color: #f0fdfa; color: #0f766e; font-size: 0.75rem;">
                                    Requested: <strong>${item.qty} ${item.uom || 'units'}</strong>
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-danger text-white border-0 shadow-sm fw-bolder d-flex align-items-center justify-content-center" 
                                        style="width: 30px; height: 30px; font-size: 18px; border-radius: 8px;" 
                                        onclick="removeItem(${index})" title="Remove item">
                                    &times;
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
            showToast("<i class='bi bi-shield-exclamation me-2'></i> Emergency Override Activated. Please state medical justification.", "danger");
        }
    </script>
</body>
</html>
