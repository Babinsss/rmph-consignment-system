<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortalController extends Controller
{
    /**
     * Display the hospital Department Requisition Portal.
     */
    public function index(): View
    {
        $items = Item::where('is_active', true)
            ->with('supplier')
            ->orderBy('name', 'asc')
            ->get();

        $departments = [
            ['name' => 'CSR', 'label' => 'CSR (Central Supply Room)', 'head_name' => 'MIA BUENVENIDA'],
            ['name' => 'HDU', 'label' => 'HDU (High Dependency Unit)', 'head_name' => 'HDU CHARGE NURSE'],
            ['name' => 'GUGMA DIALYSIS CENTER', 'label' => 'Gugma Dialysis Center', 'head_name' => 'STEPHEN ESPENOCILLA'],
            ['name' => 'PHARMACY MAIN', 'label' => 'Pharmacy Main', 'head_name' => 'SHARA PATRIA SANTOS'],
            ['name' => 'PHARMACY OPD', 'label' => 'Pharmacy OPD', 'head_name' => 'PHARMACY OPD SUPERVISOR'],
            ['name' => 'YAKAP', 'label' => 'Yakap Center', 'head_name' => 'YAKAP CLINIC HEAD'],
        ];

        $categories = $items->pluck('category')->filter()->unique()->sort()->values();

        // Calculate RMPH Operating Schedule in Asia/Manila timezone
        $now = Carbon::now('Asia/Manila');
        $dayOfWeek = $now->dayOfWeekIso; // 1 = Monday, 7 = Sunday
        $currentHour = $now->hour;

        if (in_array($dayOfWeek, [6, 7], true)) {
            $portalMode = 'CLOSED_WEEKEND';
        } elseif ($currentHour < 8 || $currentHour >= 17) {
            $portalMode = 'CLOSED_OFF_HOURS';
        } elseif (in_array($dayOfWeek, [1, 3], true)) {
            $portalMode = 'NORMAL';
        } else {
            $portalMode = 'EMERGENCY_ONLY';
        }

        return view('portal', compact('items', 'departments', 'categories', 'portalMode'));
    }

    /**
     * Process an incoming supply requisition submitted from the Department Portal.
     */
    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_name' => 'required|string|in:'.implode(',', array_keys(StockRequest::ALLOWED_REQUESTING_UNITS)),
            'requested_by' => 'required|string|max:255',
            'purpose' => 'required|string|max:1000',
            'patient_mrn' => 'nullable|string|max:100',
            'is_emergency' => 'nullable|in:0,1',
            'cart_data' => 'required|string',
        ]);

        $cart = json_decode($validated['cart_data'], true) ?? [];

        if (empty($cart)) {
            return redirect()->route('portal')->with('danger', 'Please select at least one consignment item to submit a requisition.');
        }

        $now = Carbon::now('Asia/Manila');
        $dayOfWeek = $now->dayOfWeekIso;
        $currentHour = $now->hour;
        $isEmergency = ($validated['is_emergency'] ?? '0') === '1';

        // Schedule validations (can be bypassed with Emergency Override)
        if (! $isEmergency) {
            if (in_array($dayOfWeek, [6, 7], true)) {
                return redirect()->route('portal')->with('danger', 'Standard requisitioning is closed on weekends. Activate Emergency Override if this is life-critical.');
            }
            if ($currentHour < 8 || $currentHour >= 17) {
                return redirect()->route('portal')->with('danger', 'Requisitioning is outside operating hours (8:00 AM - 5:00 PM). Activate Emergency Override if urgent.');
            }
            if (in_array($dayOfWeek, [2, 4, 5], true)) {
                return redirect()->route('portal')->with('danger', 'Today is restricted to Emergency requisitions only. Please confirm emergency justification.');
            }
        }

        $purpose = $validated['purpose'];
        if ($isEmergency && ! str_contains($purpose, '[EMERGENCY]')) {
            $purpose = '[EMERGENCY] '.$purpose;
        }

        // Determine dominant consignment stream from requested items
        $itemIds = collect($cart)->pluck('id')->filter()->all();
        $itemsMap = Item::whereIn('id', $itemIds)->get()->keyBy('id');

        if ($itemsMap->isEmpty()) {
            return redirect()->route('portal')->with('danger', 'Selected consignment catalog items could not be resolved.');
        }

        $pharmacyCount = $itemsMap->filter(fn (Item $it) => $it->department === 'pharmacy')->count();
        $medSuppliesCount = $itemsMap->filter(fn (Item $it) => $it->department === 'medical_supplies')->count();
        $departmentStream = $pharmacyCount > $medSuppliesCount ? 'pharmacy' : 'medical_supplies';

        $stockRequest = DB::transaction(function () use ($validated, $purpose, $departmentStream, $cart, $itemsMap) {
            $risNumber = 'RIS-'.date('Ymd').'-'.strtoupper(Str::random(4));

            $stockRequest = StockRequest::create([
                'request_number' => $risNumber,
                'department' => $departmentStream,
                'requesting_unit' => $validated['department_name'],
                'requester_name' => $validated['requested_by'],
                'request_date' => Carbon::today(),
                'status' => 'pending',
                'purpose' => $purpose,
                'patient_mrn' => $validated['patient_mrn'] ?? null,
                'notes' => 'Submitted via RMPH Front-Facing Department Portal',
            ]);

            foreach ($cart as $entry) {
                $itemId = $entry['id'] ?? null;
                $qty = (int) ($entry['qty'] ?? 1);

                if (! $itemId || $qty <= 0 || ! isset($itemsMap[$itemId])) {
                    continue;
                }

                $catalogItem = $itemsMap[$itemId];

                StockRequestItem::create([
                    'stock_request_id' => $stockRequest->id,
                    'item_id' => $catalogItem->id,
                    'quantity_requested' => $qty,
                    'quantity_issued' => 0,
                    'unit_cost' => (float) $catalogItem->unit_cost,
                    'total_cost' => 0.00,
                ]);
            }

            return $stockRequest;
        });

        $officerInCharge = $departmentStream === 'pharmacy' ? 'Kristine Mae Batan' : 'Aliana Marie Dula';

        return redirect()->route('portal')
            ->with('success', "Requisition {$stockRequest->request_number} submitted successfully! Awaiting review by Consignment Officer {$officerInCharge}.")
            ->with('stock_request_id', $stockRequest->id)
            ->with('request_number', $stockRequest->request_number);
    }
}
