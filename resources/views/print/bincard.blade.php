<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bin Card - {{ $item->item_code }} - {{ $item->name }}</title>
    <style>
        /* Forces standard Long Bond cut in half vertically (4.25in x 13in) */
        @page { size: 4.25in 13in; margin: 0.25in; }
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 0; color: #000; }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .header-table td { border: 1px solid #000; text-align: center; padding: 4px; }
        
        .title-cell { font-weight: bold; font-size: 13px; line-height: 1.2; }
        .doc-no-cell { font-size: 9px; }
        .rev-cell { font-weight: bold; font-size: 13px; }
        
        .item-info { font-size: 11px; font-weight: bold; margin-bottom: 6px; text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 4px; }
        .item-meta { font-size: 9px; font-weight: normal; color: #333; margin-top: 2px; }
        
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { 
            border: 1px solid #000; 
            padding: 4px 2px; 
            text-align: center; 
            font-size: 10px; 
            height: 18px; 
        }
        .data-table th { font-weight: bold; background-color: #f5f5f5; }
        .dept-col { text-align: left !important; width: 44%; padding-left: 3px; font-size: 9px; }
        
        /* Auto-hide browser print UI */
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        
        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 8px 12px;
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
            padding: 6px 12px;
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
        <span>Bin Card Preview (4.25" × 13" Long Bond 1/2)</span>
        <div>
            <a href="javascript:history.back()" class="btn" style="background: #475569; margin-right: 6px;">← Back</a>
            <button onclick="window.print()" class="btn">🖨️ Print Bin Card</button>
        </div>
    </div>

    <table class="header-table">
        <tr>
            <td rowspan="2" style="width: 15%;">
                <img src="{{ asset('images/capiz.jpg') }}" style="height: 34px;" alt="Capiz Provincial Seal" onerror="this.style.display='none'">
            </td>
            <td class="title-cell" style="width: 55%; border-bottom: none;">
                Document Title:<br>
                <strong>BIN CARD</strong>
            </td>
            <td style="width: 15%; font-size: 9px; border-bottom: none;">Rev No</td>
            <td rowspan="2" style="width: 15%;">
                <img src="{{ asset('images/rmph.jpg') }}" style="height: 34px;" alt="RMPH Seal" onerror="this.style.display='none'">
            </td>
        </tr>
        <tr>
            <td class="doc-no-cell" style="border-top: none;">Document No: RMH-PPS-F02</td>
            <td class="rev-cell" style="border-top: none;">00</td>
        </tr>
    </table>

    <div class="item-info">
        ITEM: <u>{{ $item->name }}</u>
        <div class="item-meta">
            CODE: <strong>{{ $item->item_code }}</strong> &bull;
            UOM: <strong>{{ strtoupper($item->uom) }}</strong> &bull;
            STREAM: <strong>{{ $item->department === 'pharmacy' ? 'PHARMACY' : 'MEDICAL SUPPLIES' }}</strong>
            @if($item->supplier)
                &bull; SUPPLIER: <strong>{{ $item->supplier->name }}</strong>
            @endif
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 16%;">DATE</th>
                <th class="dept-col">DEPARTMENT /<br>SUPPLIER</th>
                <th style="width: 13%;">DELIVERY<br>QTY</th>
                <th style="width: 13%;">QTY<br>ISSUED</th>
                <th style="width: 14%;">STOCK<br>BALANCE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            <tr>
                <td>{{ \Carbon\Carbon::parse($tx['date'])->format('m-d-y') }}</td>
                <td class="dept-col">{{ $tx['department'] }}</td>
                <td style="font-weight: bold; color: {{ !empty($tx['delivery_qty']) ? '#047857' : '#000' }};">{{ $tx['delivery_qty'] ?: '' }}</td>
                <td style="font-weight: bold; color: {{ !empty($tx['qty_issued']) ? '#b91c1c' : '#000' }};">{{ $tx['qty_issued'] ?: '' }}</td>
                <td style="font-weight: bold;">{{ $tx['balance'] }}</td>
            </tr>
            @endforeach
            
            {{-- Print empty rows to fill the card up to 35 total lines --}}
            @for($i = count($transactions); $i < 35; $i++)
            <tr>
                <td></td>
                <td class="dept-col"></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div style="margin-top: 10px; font-size: 8px; text-align: center; color: #555;">
        Roxas Memorial Provincial Hospital &bull; Hospital Extensive Consignment System (HECS) &bull; RMH-PPS-F02 Rev. 00
    </div>

</body>
</html>
