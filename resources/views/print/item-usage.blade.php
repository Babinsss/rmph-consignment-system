<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Item Usage Report - {{ $item->item_code }} - {{ $item->name }}</title>
    <style>
        @page { size: 8.5in 11in; margin: 0.5in; }
        body { font-family: Arial, sans-serif; color: #000; font-size: 12px; line-height: 1.4; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 16px; position: relative; }
        .logo-left { position: absolute; left: 0; top: 0; height: 60px; }
        .logo-right { position: absolute; right: 0; top: 0; height: 60px; }
        .title h2 { margin: 0; font-size: 16px; font-weight: bold; }
        .title h3 { margin: 4px 0 0; font-size: 14px; text-decoration: underline; text-transform: uppercase; }
        .title p { margin: 4px 0 0; font-weight: bold; font-size: 12px; }
        
        .meta-info { margin-bottom: 16px; font-size: 12px; }
        .meta-info table { width: 100%; border-collapse: collapse; }
        .meta-info td { padding: 4px 6px; }

        .report-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 11px; }
        .report-table th, .report-table td { border: 1px solid #000; padding: 6px 10px; text-align: left; }
        .report-table th { background-color: #f1f5f9; text-transform: uppercase; font-size: 10px; font-weight: bold; text-align: center; }
        .report-table td.qty-col { text-align: center; font-weight: bold; font-size: 12px; }
        .report-table td.rank-col { text-align: center; font-weight: bold; color: #555; width: 10%; }

        .signatories { display: flex; justify-content: space-between; padding-top: 25px; page-break-inside: avoid; }
        .sig-block { width: 45%; text-align: left; }
        .sig-name { font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-top: 35px; font-size: 11px; }
        .sig-title { font-size: 10px; color: #333; }
        
        @media print { 
            body { padding: 0; } 
            .no-print { display: none !important; }
        }

        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 12px;
        }
        .btn {
            background: #0d9488;
            color: #fff;
            border: none;
            padding: 6px 14px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="no-print-bar no-print">
        <span>Departmental Consignment Item Usage Report &bull; Month: {{ $reportMonth }} {{ $reportYear }}</span>
        <div>
            <a href="javascript:history.back()" class="btn" style="background: #475569; margin-right: 6px;">← Back</a>
            <button onclick="window.print()" class="btn">🖨️ Print Usage Report</button>
        </div>
    </div>

    <div class="header">
        <img src="{{ asset('images/capiz.jpg') }}" class="logo-left" alt="Capiz Provincial Seal" onerror="this.style.display='none'">
        <div class="title">
            <h2>ROXAS MEMORIAL PROVINCIAL HOSPITAL</h2>
            <div style="font-size: 11px; font-weight: normal; color: #444;">Capiz Provincial Government &bull; Consignment Inventory Division</div>
            <h3>Departmental Consignment Item Usage Report</h3>
            <p>FOR THE MONTH OF {{ strtoupper($reportMonth) }} {{ $reportYear }}</p>
        </div>
        <img src="{{ asset('images/rmph.jpg') }}" class="logo-right" alt="RMPH Seal" onerror="this.style.display='none'">
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 15%;"><strong>Consignment Item:</strong></td>
                <td style="width: 35%; border-bottom: 1px solid #000; font-weight: bold; font-size: 13px;">{{ $item->name }}</td>
                <td style="width: 15%; text-align: right; padding-right: 10px;"><strong>Item Code:</strong></td>
                <td style="width: 35%; border-bottom: 1px solid #000; font-weight: bold;">{{ $item->item_code }} ({{ strtoupper($item->uom) }})</td>
            </tr>
            <tr>
                <td><strong>Consignment Stream:</strong></td>
                <td style="border-bottom: 1px solid #000;">{{ $item->department === 'pharmacy' ? 'Pharmacy Stocks' : 'Medical Supplies' }}</td>
                <td style="text-align: right; padding-right: 10px;"><strong>Consignor:</strong></td>
                <td style="border-bottom: 1px solid #000;">{{ $item->supplier?->name ?? 'Accredited Supplier' }}</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th class="rank-col">Rank</th>
                <th style="width: 55%;">Authorized Requesting Department / Unit</th>
                <th style="width: 20%;">Total Quantity Consumed</th>
                <th style="width: 15%;">Equivalent Value</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotalQty = 0; 
                $grandTotalValue = 0;
            @endphp
            @forelse($usageData as $index => $data)
                @php 
                    $lineValue = $data->total_requested * (float) $item->unit_cost;
                    $grandTotalQty += $data->total_requested;
                    $grandTotalValue += $lineValue;
                @endphp
                <tr>
                    <td class="rank-col">#{{ $index + 1 }}</td>
                    <td style="font-weight: bold; text-transform: uppercase;">
                        {{ $data->department_name }}
                    </td>
                    <td class="qty-col">{{ number_format($data->total_requested) }} {{ $item->uom }}</td>
                    <td style="text-align: right; font-weight: bold;">₱{{ number_format($lineValue, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 25px; font-style: italic; color: #666;">
                        No authorized department issuances recorded for this consignment item in {{ $reportMonth }} {{ $reportYear }}.
                    </td>
                </tr>
            @endforelse
            
            @if($usageData->isNotEmpty())
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="2" style="text-align: right; text-transform: uppercase; padding-right: 15px;">Total Consignment Consumption:</td>
                <td class="qty-col" style="font-size: 13px; color: #0f766e;">{{ number_format($grandTotalQty) }} {{ $item->uom }}</td>
                <td style="text-align: right; font-size: 13px; color: #047857;">₱{{ number_format($grandTotalValue, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="signatories">
        <div class="sig-block">
            <div>Prepared & Certified Correct by:</div>
            <div class="sig-name">
                @if($item->department === 'pharmacy')
                    KRISTINE MAE BATAN
                @else
                    ALIANA MARIE DULA
                @endif
            </div>
            <div class="sig-title">
                @if($item->department === 'pharmacy')
                    Consignment Officer - Pharmacy Stocks
                @else
                    Consignment Officer - Medical Supplies
                @endif
            </div>
        </div>
        <div class="sig-block" style="text-align: right;">
            <div style="text-align: left; display: inline-block;">
                <div>Noted by:</div>
                <div class="sig-name">FLORENCIO O. LUCHING JR., M.D., FPCS</div>
                <div class="sig-title">Chief of Hospital II &bull; Roxas Memorial Provincial Hospital</div>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 9px; color: #666;">
        Generated by RMPH Consignment System on {{ now()->format('F d, Y h:i A') }} &bull; Authorized Departments: CSR, HDU, Gugma Dialysis, Pharmacy Main/OPD, Yakap
    </div>

</body>
</html>
