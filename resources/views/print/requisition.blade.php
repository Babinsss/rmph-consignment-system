<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIS Slip - {{ $request->request_number }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1a202c;
        }

        body {
            background-color: #f7fafc;
            padding: 24px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .no-print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background-color: #0d9488;
            color: #ffffff;
        }

        .btn-secondary {
            background-color: #e2e8f0;
            color: #4a5568;
            margin-right: 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 16px;
        }

        .hospital-name {
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f766e;
        }

        .doc-title {
            font-size: 16px;
            font-weight: 700;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px 24px;
            margin-bottom: 24px;
            padding: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
        }

        .info-row {
            display: flex;
        }

        .info-label {
            width: 150px;
            font-weight: 600;
            color: #64748b;
        }

        .info-value {
            font-weight: 600;
            color: #1e293b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 32px;
        }

        th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-align: left;
            text-transform: uppercase;
            font-size: 11px;
        }

        td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }

        .signatures {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-top: 48px;
            page-break-inside: avoid;
        }

        .sig-box {
            text-align: center;
        }

        .sig-line {
            border-bottom: 1px solid #000000;
            height: 40px;
            margin-bottom: 6px;
        }

        .sig-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        .sig-name {
            font-size: 11px;
            font-weight: 700;
            margin-top: 4px;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: portrait;
                margin: 12mm;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="no-print-bar no-print">
        <a href="javascript:history.back()" class="btn btn-secondary">← Back to System</a>
        <div>
            <button onclick="window.print()" class="btn btn-primary">🖨️ Print Requisition Slip</button>
        </div>
    </div>

    <div class="header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0d9488; padding-bottom: 16px; margin-bottom: 24px; text-align: left;">
        <div>
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 700;">Republic of the Philippines • Capiz Provincial Government</div>
            <div class="hospital-name" style="font-size: 20px; font-weight: 800; color: #0f766e;">ROXAS MEMORIAL PROVINCIAL HOSPITAL</div>
            <div class="doc-title" style="font-size: 15px; font-weight: 700; margin-top: 4px;">REQUISITION AND ISSUE SLIP (RIS) - CONSIGNMENT STOCKS</div>
        </div>
        <div style="text-align: right;">
            <svg style="height: 38px; width: 140px; display: inline-block;" viewBox="0 0 140 38">
                <rect x="0" y="0" width="140" height="38" fill="white"/>
                <rect x="8" y="4" width="4" height="30" fill="black"/>
                <rect x="15" y="4" width="2" height="30" fill="black"/>
                <rect x="20" y="4" width="3" height="30" fill="black"/>
                <rect x="27" y="4" width="1" height="30" fill="black"/>
                <rect x="31" y="4" width="4" height="30" fill="black"/>
                <rect x="38" y="4" width="2" height="30" fill="black"/>
                <rect x="44" y="4" width="5" height="30" fill="black"/>
                <rect x="52" y="4" width="2" height="30" fill="black"/>
                <rect x="58" y="4" width="4" height="30" fill="black"/>
                <rect x="65" y="4" width="2" height="30" fill="black"/>
                <rect x="71" y="4" width="3" height="30" fill="black"/>
                <rect x="77" y="4" width="5" height="30" fill="black"/>
                <rect x="85" y="4" width="2" height="30" fill="black"/>
                <rect x="91" y="4" width="4" height="30" fill="black"/>
                <rect x="98" y="4" width="1" height="30" fill="black"/>
                <rect x="103" y="4" width="4" height="30" fill="black"/>
                <rect x="110" y="4" width="2" height="30" fill="black"/>
                <rect x="116" y="4" width="4" height="30" fill="black"/>
                <rect x="123" y="4" width="3" height="30" fill="black"/>
            </svg>
            <div style="font-family: monospace; font-size: 11px; letter-spacing: 2px; font-weight: bold; color: #334155;">{{ $request->request_number }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">RIS Slip Number:</span>
            <span class="info-value font-bold">{{ $request->request_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Requisition Date:</span>
            <span class="info-value">{{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->format('F d, Y') : now()->format('F d, Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Consignment Stream:</span>
            <span class="info-value">{{ ucfirst($request->department) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value font-bold" style="text-transform: uppercase;">{{ $request->status }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Requesting Unit/Ward:</span>
            <span class="info-value">{{ $request->requesting_unit }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Purpose:</span>
            <span class="info-value">{{ $request->purpose ?? 'Ward Supply Stocking' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Requisitioner Name:</span>
            <span class="info-value">{{ $request->requester_name }}</span>
        </div>
        @if($request->patient_mrn)
        <div class="info-row">
            <span class="info-label">Patient MRN:</span>
            <span class="info-value">{{ $request->patient_mrn }}</span>
        </div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;" class="text-center">#</th>
                <th style="width: 120px;">Item Code</th>
                <th>Item Description / Specification</th>
                <th style="width: 70px;" class="text-center">UOM</th>
                <th style="width: 80px;" class="text-right">Qty Req.</th>
                <th style="width: 80px;" class="text-right">Qty Issued</th>
                <th style="width: 90px;" class="text-right">Unit Cost</th>
                <th style="width: 100px;" class="text-right">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($request->items as $idx => $item)
                @php $grandTotal += $item->total_cost; @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $item->item->item_code }}</td>
                    <td>
                        {{ $item->item->name }}
                        @if($item->item->generic_name)
                            <br><small style="color: #64748b;">Generic: {{ $item->item->generic_name }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ ucfirst($item->item->uom) }}</td>
                    <td class="text-right">{{ $item->quantity_requested }}</td>
                    <td class="text-right font-bold">{{ $item->quantity_issued }}</td>
                    <td class="text-right">₱{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="text-right font-bold">₱{{ number_format($item->total_cost, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 24px; color: #94a3b8;">
                        No items attached to this requisition slip.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td colspan="7" class="text-right">TOTAL CONSIGNMENT VALUATION:</td>
                <td class="text-right">₱{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Requisitioned By (Ward)</div>
            <div class="sig-name">{{ $request->requester_name }}</div>
            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">{{ $request->requesting_unit }}</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Approved By</div>
            <div class="sig-name">{{ $request->approvedBy?->name ?? 'FLORENCIO O. LUCHING JR., M.D.' }}</div>
            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">Chief of Hospital II</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Issued By (Consignment Officer)</div>
            <div class="sig-name">
                {{ $request->issuedBy?->name ?? ($request->department === 'pharmacy' ? 'KRISTINE MAE BATAN' : 'ALIANA MARIE DULA') }}
            </div>
            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">
                {{ $request->department === 'pharmacy' ? 'Consignment Officer - Pharmacy' : 'Consignment Officer - Medical Supplies' }}
            </div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Received By (Ward Staff)</div>
            <div class="sig-name">Signature over Printed Name</div>
            <div style="font-size: 9px; color: #64748b; margin-top: 2px;">Authorized Unit Staff</div>
        </div>
    </div>
</div>

</body>
</html>
