<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Request (Appendix 47) - RMPH Consignment Replenishment</title>
    <style>
        @media print {
            @page { size: 8.5in 11in; margin: 0.4in; }
            body { margin: 0; padding: 0; background: white !important; }
            .print-btn-container { display: none !important; }
            .document-container { box-shadow: none !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
        }
        
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 12px; 
            background: #525659;
            display: flex;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        .document-container {
            background: white;
            width: 8.5in;
            min-height: 11in;
            padding: 0.4in;
            box-shadow: 0 0 15px rgba(0,0,0,0.4);
            box-sizing: border-box;
            position: relative;
        }

        table.pr-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid black;
        }
        table.pr-table th, table.pr-table td {
            border: 1px solid black;
            padding: 4px 6px;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-italic { font-style: italic; }
        
        .col-1 { width: 8%; }
        .col-2 { width: 8%; }
        .col-3 { width: 44%; }
        .col-4 { width: 10%; }
        .col-5 { width: 14%; }
        .col-6 { width: 16%; }

        .empty-row td { height: 21px; }
        
        .header-cell {
            padding: 12px 10px !important;
            position: relative;
        }
    </style>
</head>
<body>
    
    <div class="print-btn-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        <button onclick="window.print()" style="padding: 10px 18px; font-size: 14px; cursor: pointer; font-weight: bold; background: #0d9488; color: white; border: none; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">🖨️ Print Purchase Request</button>
        <button onclick="window.history.back()" style="padding: 10px 16px; font-size: 14px; cursor: pointer; margin-left: 8px; border-radius: 5px; border: 1px solid #ccc; background: white;">Close</button>
    </div>

    <div class="document-container">
        <table class="pr-table">
            
            <!-- ROW 1: HEADER SECTION -->
            <tr>
                <td colspan="6" class="header-cell">
                    <div style="position: absolute; top: 6px; right: 15px; font-style: italic; font-size: 11px;">Appendix 47</div>
                    
                    <div style="position: absolute; left: 35px; top: 12px;">
                        <img src="{{ asset('images/capiz.jpg') }}" onerror="this.style.display='none'" style="width: 65px; height: 65px; border-radius: 50%;">
                    </div>
                    <div style="position: absolute; right: 35px; top: 12px;">
                        <img src="{{ asset('images/rmph.jpg') }}" onerror="this.style.display='none'" style="width: 65px; height: 65px; border-radius: 50%;">
                    </div>

                    <div class="text-center">
                        <div style="font-size: 12px;">Republic of the Philippines</div>
                        <div style="font-size: 14px; font-weight: bold;">PROVINCIAL GOVERNMENT OF CAPIZ</div>
                        <div style="font-size: 12px;">Capitol Building, Roxas City</div>
                        <h2 style="margin: 10px 0 0 0; font-size: 20px; font-weight: bold; letter-spacing: 1px;">PURCHASE REQUEST</h2>
                    </div>

                    <div style="position: absolute; bottom: 6px; right: 90px; font-size: 11px; font-weight: bold;">
                        Fund: <span style="display:inline-block; width: 80px; border-bottom: 1px solid black; text-align: center;">CONSIGNMENT</span>
                    </div>
                </td>
            </tr>

            <!-- ROW 2 & 3: METADATA -->
            <tr>
                <td colspan="2"><span class="font-bold">Department:</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="font-bold">RMPH</span></td>
                <td colspan="1"><span class="font-bold">P.R. No.:</span> &nbsp;PR-{{ now()->format('Ymd') }}-{{ rand(100, 999) }}</td>
                <td colspan="3"><span class="font-bold">Date:</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ date('F d, Y') }}</td>
            </tr>
            <tr>
                <td colspan="2"><span class="font-bold">Section:</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CONSIGNMENT OPERATIONS</td>
                <td colspan="1"><span class="font-bold">SAI No. / Stream :</span> &nbsp;{{ strtoupper($department ?? 'Consignment Inventory') }}</td>
                <td colspan="3"></td>
            </tr>

            <!-- ROW 4: TABLE HEADERS -->
            <tr class="text-center font-bold">
                <td class="col-1">Item No.</td>
                <td class="col-2">Unit</td>
                <td class="col-3">Item Description</td>
                <td class="col-4">Quantity</td>
                <td class="col-5">Unit Cost (₱)</td>
                <td class="col-6">Total Cost (₱)</td>
            </tr>

            <!-- ITEM DATA ROWS -->
            @php 
                $total = 0; 
                $maxRows = 16;
                $itemCount = count($items);
            @endphp

            @foreach($items as $index => $item)
                @php 
                    $suggestedQty = max($item['suggested_qty'] ?? 100, 1);
                    $lineTotal = $suggestedQty * ($item['unit_cost'] ?? 0);
                    $total += $lineTotal;
                @endphp
                <tr class="empty-row">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center" style="text-transform: uppercase;">{{ $item['uom'] ?? 'pcs' }}</td>
                    <td style="padding-left: 8px;">
                        <strong>{{ $item['name'] }}</strong>
                        @if(!empty($item['generic_name']))
                            <span style="font-size: 10px; font-style: italic;">({{ $item['generic_name'] }})</span>
                        @endif
                        <span style="font-size: 9px; color: #555;">&bull; Code: {{ $item['item_code'] }} &bull; Supplier: {{ $item['supplier_name'] ?? 'Consignor' }}</span>
                    </td>
                    <td class="text-center font-bold">{{ number_format($suggestedQty) }}</td>
                    <td class="text-right">{{ number_format($item['unit_cost'], 2) }}</td>
                    <td class="text-right">{{ number_format($lineTotal, 2) }}</td>
                </tr>
            @endforeach

            <!-- BLANK ROWS TO FILL THE PAGE -->
            @for ($i = $itemCount; $i < $maxRows; $i++)
                <tr class="empty-row">
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            @endfor

            <!-- FOOTER DELIVERY TERMS -->
            <tr class="empty-row">
                <td></td>
                <td></td>
                <td><span class="font-italic">Schedule of Delivery: 15 to 30 days upon receipt of Notice to Proceed (NTP) / Consignment Order</span></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="empty-row">
                <td></td>
                <td></td>
                <td><span class="font-italic">Place of Delivery: PGSO to RMPH Consignment Operations</span></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="empty-row">
                <td></td>
                <td></td>
                <td class="text-center"><span class="font-italic">X-X-X-X-X-X-X-X-X-X-X-X-X-X-X-X-X-X-X-X-X</span></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            <!-- TOTAL ROW -->
            <tr>
                <td colspan="5" class="text-right font-bold" style="padding-right: 20px;">TOTAL ESTIMATED COST:</td>
                <td class="text-right font-bold">
                    <span style="float: left;">₱</span> {{ number_format($total, 2) }}
                </td>
            </tr>

            <!-- PURPOSE ROW -->
            <tr>
                <td colspan="2"><span class="font-bold">Purpose:</span></td>
                <td colspan="4"><span class="font-bold font-italic" style="text-decoration: underline;">For replenishment of Consignment Inventory stocks for RMPH clinical wards & patient care</span></td>
            </tr>

            <!-- SIGNATORIES HEADERS -->
            <tr>
                <td colspan="2"></td>
                <td colspan="1" class="text-center font-italic">Requested by:</td>
                <td colspan="3" class="text-center font-italic">Approved by:</td>
            </tr>
            
            <!-- SIGNATURE SPACING -->
            <tr>
                <td colspan="2">Signature :</td>
                <td colspan="1" style="padding: 18px;"></td>
                <td colspan="3" style="padding: 18px;"></td>
            </tr>

            <!-- NAMES -->
            <tr>
                <td colspan="2">Printed Name :</td>
                <td colspan="1" class="text-center font-bold">FLORENCIO O. LUCHING JR., M.D., FPCS</td>
                <td colspan="3" class="text-center font-bold">FREDENIL H. CASTRO</td>
            </tr>

            <!-- DESIGNATIONS -->
            <tr>
                <td colspan="2">Designation :</td>
                <td colspan="1" class="text-center font-italic">Chief of Hospital II &bull; RMPH</td>
                <td colspan="3" class="text-center font-italic">Provincial Governor &bull; Province of Capiz</td>
            </tr>
        </table>

        <!-- COPIES NOTE -->
        <div style="font-size: 10px; font-style: italic; margin-top: 4px; display: flex; justify-content: space-between;">
            <span>PR shall be prepared in four (4) copies</span>
            <span>Roxas Memorial Provincial Hospital &bull; HECS Appendix 47</span>
        </div>
    </div>

</body>
</html>
