<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stockcard - {{ $item->item_code }} - {{ $item->name }}</title>
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
            max-width: 1000px;
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
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #0f766e;
        }

        .doc-title {
            font-size: 18px;
            font-weight: 700;
            margin-top: 4px;
            letter-spacing: 1px;
        }

        .stream-tag {
            display: inline-block;
            margin-top: 6px;
            padding: 2px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            background: #ccfbf1;
            color: #0f766e;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px 24px;
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
            width: 140px;
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
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
            text-transform: uppercase;
            font-size: 11px;
        }

        td {
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .text-green { color: #047857; }
        .text-red { color: #b91c1c; }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
            margin-top: 48px;
            page-break-inside: avoid;
        }

        .sig-box {
            text-align: center;
        }

        .sig-line {
            border-bottom: 1px solid #000000;
            height: 40px;
            margin-bottom: 8px;
        }

        .sig-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        .sig-name {
            font-size: 12px;
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
            <button onclick="window.print()" class="btn btn-primary">🖨️ Print Stockcard</button>
        </div>
    </div>

    <div class="header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0d9488; padding-bottom: 16px; margin-bottom: 24px; text-align: left;">
        <div>
            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 700;">Republic of the Philippines • Capiz Provincial Government</div>
            <div class="hospital-name" style="font-size: 20px; font-weight: 800; color: #0f766e;">ROXAS MEMORIAL PROVINCIAL HOSPITAL</div>
            <div class="doc-title" style="font-size: 14px; font-weight: 700; margin-top: 4px; color: #334155;">GENERAL FORM NO. 18 • CONSIGNMENT STOCK CARD</div>
            <div class="stream-tag" style="display: inline-block; margin-top: 6px; padding: 2px 12px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: #ccfbf1; color: #0f766e;">
                {{ $item->department === 'pharmacy' ? 'Pharmacy Department' : 'Medical Supplies / Central CSR' }}
            </div>
        </div>
        <div style="text-align: right;">
            <svg style="height: 38px; width: 140px; display: inline-block;" viewBox="0 0 140 38">
                <rect x="0" y="0" width="140" height="38" fill="white"/>
                <rect x="10" y="4" width="3" height="30" fill="black"/>
                <rect x="16" y="4" width="2" height="30" fill="black"/>
                <rect x="22" y="4" width="4" height="30" fill="black"/>
                <rect x="29" y="4" width="1" height="30" fill="black"/>
                <rect x="33" y="4" width="3" height="30" fill="black"/>
                <rect x="39" y="4" width="5" height="30" fill="black"/>
                <rect x="47" y="4" width="2" height="30" fill="black"/>
                <rect x="52" y="4" width="4" height="30" fill="black"/>
                <rect x="59" y="4" width="2" height="30" fill="black"/>
                <rect x="64" y="4" width="3" height="30" fill="black"/>
                <rect x="70" y="4" width="5" height="30" fill="black"/>
                <rect x="78" y="4" width="2" height="30" fill="black"/>
                <rect x="83" y="4" width="4" height="30" fill="black"/>
                <rect x="90" y="4" width="1" height="30" fill="black"/>
                <rect x="94" y="4" width="3" height="30" fill="black"/>
                <rect x="100" y="4" width="4" height="30" fill="black"/>
                <rect x="107" y="4" width="2" height="30" fill="black"/>
                <rect x="112" y="4" width="5" height="30" fill="black"/>
                <rect x="120" y="4" width="2" height="30" fill="black"/>
                <rect x="125" y="4" width="4" height="30" fill="black"/>
            </svg>
            <div style="font-family: monospace; font-size: 11px; letter-spacing: 2px; font-weight: bold; color: #334155;">{{ $item->item_code }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <span class="info-label">Item SKU / Code:</span>
            <span class="info-value">{{ $item->item_code }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Unit of Measure:</span>
            <span class="info-value">{{ ucfirst($item->uom) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Item Description:</span>
            <span class="info-value">{{ $item->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Reorder Min Level:</span>
            <span class="info-value">{{ $item->minimum_stock_level }} {{ $item->uom }}s</span>
        </div>
        @if($item->generic_name)
        <div class="info-row">
            <span class="info-label">Generic Name:</span>
            <span class="info-value">{{ $item->generic_name }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Consignment Cost:</span>
            <span class="info-value">₱{{ number_format($item->unit_cost, 2) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Primary Consignor:</span>
            <span class="info-value">{{ $item->supplier?->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Available Stock:</span>
            <span class="info-value font-bold {{ $item->current_stock <= $item->minimum_stock_level ? 'text-red' : 'text-green' }}">
                {{ $item->current_stock }} {{ $item->uom }}s
            </span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Date</th>
                <th style="width: 100px;">Reference</th>
                <th>Source / Destination</th>
                <th style="width: 90px;">Lot / Batch</th>
                <th style="width: 75px;">Expiry</th>
                <th class="text-right" style="width: 60px;">In</th>
                <th class="text-right" style="width: 60px;">Out</th>
                <th class="text-right" style="width: 70px;">Balance</th>
                <th class="text-right" style="width: 75px;">Unit Cost</th>
                <th style="width: 80px;">Custodian</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('Y-m-d') }}</td>
                    <td class="font-bold">{{ $m->reference_number }}</td>
                    <td>{{ $m->source_destination }}</td>
                    <td>{{ $m->lot_number ?? '—' }}</td>
                    <td>{{ $m->expiry_date ? $m->expiry_date->format('M Y') : '—' }}</td>
                    <td class="text-right font-bold text-green">{{ $m->quantity_in > 0 ? '+' . $m->quantity_in : '—' }}</td>
                    <td class="text-right font-bold text-red">{{ $m->quantity_out > 0 ? '-' . $m->quantity_out : '—' }}</td>
                    <td class="text-right font-bold">{{ $m->balance_after }}</td>
                    <td class="text-right">₱{{ number_format($m->unit_cost, 2) }}</td>
                    <td>{{ $m->user?->name ?? 'System' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 24px; color: #94a3b8;">
                        No recorded movements yet for this consignment item.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Prepared / Recorded By</div>
            <div class="sig-name">
                @if($item->department === 'pharmacy')
                    KRISTINE MAE BATAN
                @else
                    ALIANA MARIE DULA
                @endif
            </div>
            <div class="sig-label" style="font-size: 9px; margin-top: 2px;">
                {{ $item->department === 'pharmacy' ? 'Consignment Officer - Pharmacy Stocks' : 'Consignment Officer - Medical Supplies' }}
            </div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Verified By</div>
            <div class="sig-name">{{ Auth::user()?->name ?? 'Consignment Operations' }}</div>
            <div class="sig-label" style="font-size: 9px; margin-top: 2px;">Consignment Custodian</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Noted / Approved By</div>
            <div class="sig-name">FLORENCIO O. LUCHING JR., M.D., FPCS</div>
            <div class="sig-label" style="font-size: 9px; margin-top: 2px;">Chief of Hospital II &bull; RMPH</div>
        </div>
    </div>
</div>

</body>
</html>
