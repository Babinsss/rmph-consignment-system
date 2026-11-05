<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RAPC - Report on Actual Physical Count - {{ $consignorName ?? 'Consignment' }}</title>
    <style>
        /* Forces standard Long Bond Landscape (13in x 8.5in) */
        @page { 
            size: 13in 8.5in; 
            margin-top: 0.4in;
            margin-right: 0.5in;
            margin-bottom: 0.4in;
            margin-left: 0.6in;
        }
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; color: #000; }
        
        .header { display: flex; justify-content: space-between; margin-bottom: 15px; text-transform: uppercase; line-height: 1.4; font-size: 10px; }
        .header-left, .header-right { width: 35%; }
        .header-center { width: 30%; text-align: center; font-size: 15px; font-weight: bold; padding-top: 5px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 4px; text-align: center; font-size: 10px; }
        .data-table th { font-weight: bold; background-color: #f8fafc; }
        .desc-col { text-align: left !important; width: 28%; font-size: 9.5px; padding-left: 5px; }
        
        .signatories { display: flex; justify-content: space-between; padding-top: 15px; page-break-inside: avoid; }
        .sig-block { width: 45%; text-align: left; }
        .sig-name { font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-top: 35px; font-size: 11px; }
        .sig-title { font-size: 10px; color: #333; }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        
        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
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
        <span>RAPC Preview (13" × 8.5" Long Bond Landscape) &bull; Year: {{ $year }} &bull; Stream: {{ strtoupper(str_replace('_', ' ', $department)) }}</span>
        <div>
            <a href="javascript:history.back()" class="btn" style="background: #475569; margin-right: 6px;">← Back</a>
            <button onclick="window.print()" class="btn">🖨️ Print RAPC Sheet</button>
        </div>
    </div>

    <div class="header">
        <div class="header-left">
            <div>HOSPITAL: <b>ROXAS MEMORIAL PROVINCIAL HOSPITAL</b></div>
            <div>STREAM: <b>{{ $department === 'pharmacy' ? 'PHARMACY CONSIGNMENT' : 'MEDICAL SUPPLIES CONSIGNMENT' }}</b></div>
            <div>FOR THE YEAR: <b>{{ $year }}</b></div>
        </div>
        
        <div class="header-center">
            <div style="font-size: 11px; font-weight: normal; letter-spacing: 1px;">PROVINCE OF CAPIZ</div>
            REPORT ON ACTUAL PHYSICAL COUNT
            <div style="font-size: 9px; font-weight: normal; margin-top: 2px;">(CONSIGNMENT INVENTORY RECAPITULATION)</div>
        </div>
        
        <div class="header-right" style="text-align: right;">
            <div>FUND: <b>MOOE - CONSIGNMENT EXPENSES</b></div>
            <div>SUPPLIER: <b>{{ $consignorName ?? 'ALL ACCREDITED CONSIGNORS' }}</b></div>
            <div>AS OF: <b>{{ now()->format('F d, Y') }}</b></div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 4%;">ITEM NO.</th>
                <th colspan="4">CATALOG & CONSIGNMENT COST</th>
                <th colspan="12">RECAPITULATION (ACTUAL PHYSICAL COUNT ENDING BALANCES)</th>
            </tr>
            <tr>
                <th class="desc-col">DESCRIPTION</th>
                <th style="width: 6%;">UNIT COST</th>
                <th style="width: 5%;">STOCK</th>
                <th style="width: 5%;">UOM</th>
                <th>JAN</th><th>FEB</th><th>MAR</th><th>APR</th><th>MAY</th><th>JUN</th>
                <th>JUL</th><th>AUG</th><th>SEP</th><th>OCT</th><th>NOV</th><th>DEC</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $i)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="desc-col">
                    <strong>{{ $i['name'] }}</strong>
                    <div style="font-size: 8px; color: #555;">{{ $i['item_code'] }} &bull; {{ $i['supplier_name'] }}</div>
                </td>
                <td>₱{{ number_format($i['unit_cost'], 2) }}</td>
                <td style="font-weight: bold;">{{ $i['current_stock'] }}</td>
                <td style="text-transform: uppercase;">{{ $i['uom'] }}</td>
                
                @for($m = 1; $m <= 12; $m++)
                    <td>{{ $i['monthly_balances'][$m] !== '' ? $i['monthly_balances'][$m] : '—' }}</td>
                @endfor
            </tr>
            @endforeach
            
            {{-- Print empty placeholder rows if few items --}}
            @for($r = count($items); $r < 12; $r++)
            <tr>
                <td>{{ $r + 1 }}</td><td class="desc-col"></td><td></td><td></td><td></td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div class="signatories">
        <div class="sig-block">
            <div>Prepared and Certified Correct by:</div>
            <div class="sig-name">
                @if($department === 'pharmacy')
                    KRISTINE MAE BATAN
                @else
                    ALIANA MARIE DULA
                @endif
            </div>
            <div class="sig-title">
                @if($department === 'pharmacy')
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

    <div style="margin-top: 15px; text-align: center; font-size: 8px; color: #666;">
        Roxas Memorial Provincial Hospital &bull; Hospital Extensive Consignment System (HECS) &bull; RAPC COA Standard
    </div>

</body>
</html>
