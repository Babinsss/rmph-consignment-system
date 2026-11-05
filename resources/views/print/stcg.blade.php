<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statement of Consumed Goods ({{ $stcgNumber }}) - {{ $supplier->name }}</title>
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
            max-width: 960px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px;
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
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
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
        }

        .hospital-header {
            text-align: center;
            margin-bottom: 24px;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 16px;
        }

        .hospital-header h4 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }

        .hospital-header h2 {
            font-size: 22px;
            font-weight: 800;
            color: #0f766e;
            margin: 4px 0;
        }

        .hospital-header h3 {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hospital-header p {
            font-size: 12px;
            color: #475569;
            margin-top: 4px;
        }

        .stcg-box {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
            background: #f8fafc;
            padding: 16px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 12px;
        }

        .stcg-box div {
            margin-bottom: 4px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 30px;
        }

        .report-table th, .report-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 8px;
        }

        .report-table th {
            background-color: #f1f5f9;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            color: #334155;
            text-align: left;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #cbd5e1;
        }

        .sign-box {
            text-align: center;
            font-size: 11px;
        }

        .sign-line {
            margin-top: 45px;
            border-top: 1px solid #1a202c;
            padding-top: 4px;
            font-weight: 700;
        }

        .sign-title {
            font-size: 10px;
            color: #64748b;
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

            .no-print-bar {
                display: none;
            }

            @page {
                size: portrait;
                margin: 15mm 12mm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Action Bar -->
        <div class="no-print-bar">
            <div>
                <a href="/admin/statement-of-consumed-goods" class="btn btn-secondary">← Back to StCG Generator</a>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-primary">🖨️ Print StCG Voucher</button>
            </div>
        </div>

        <!-- Official Header -->
        <div class="hospital-header">
            <h4>Republic of the Philippines • Capiz Provincial Government</h4>
            <h2>ROXAS MEMORIAL PROVINCIAL HOSPITAL</h2>
            <h3>STATEMENT OF CONSUMED GOODS (StCG)</h3>
            <p>Hospital Extensive Consignment System • Roxas City, Capiz</p>
        </div>

        <!-- Metadata Box -->
        <div class="stcg-box">
            <div>
                <div><strong>Consignor / Supplier:</strong> {{ $supplier->name }}</div>
                <div><strong>Address:</strong> {{ $supplier->address ?? 'Western Visayas, Philippines' }}</div>
                <div><strong>Payment Terms:</strong> {{ $supplier->payment_terms ?? '30 days post-consumption' }}</div>
            </div>
            <div style="text-align: right;">
                <div><strong>StCG Reference No:</strong> <span style="font-family: monospace; font-weight: 700; color: #0f766e;">{{ $stcgNumber }}</span></div>
                <div><strong>Billing Period:</strong> {{ $dateFrom }} to {{ $dateTo }}</div>
                <div><strong>Date Issued:</strong> {{ now()->format('M d, Y') }}</div>
            </div>
        </div>

        <!-- Itemized Consumed List -->
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Item Code</th>
                    <th style="width: 40%;">Item Description</th>
                    <th style="width: 10%;">UOM</th>
                    <th class="text-right" style="width: 15%;">Consignor Unit Price</th>
                    <th class="text-right" style="width: 10%;">Qty Consumed</th>
                    <th class="text-right" style="width: 15%;">Total Payable (PHP)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td style="font-weight: 700; font-family: monospace;">{{ $row['item_code'] }}</td>
                        <td>
                            <strong>{{ $row['item_name'] }}</strong>
                            @if($row['generic_name'])
                                <br><small style="color: #64748b;">Generic: {{ $row['generic_name'] }}</small>
                            @endif
                        </td>
                        <td>{{ ucfirst($row['uom']) }}</td>
                        <td class="text-right">₱{{ number_format($row['unit_cost'], 2) }}</td>
                        <td class="text-right" style="font-weight: 700; color: #d97706;">{{ number_format($row['quantity_consumed']) }}</td>
                        <td class="text-right" style="font-weight: 800; color: #047857;">₱{{ number_format($row['total_amount'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 24px; color: #94a3b8;">
                            No consumption records found for this consignor within the selected billing period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: 800; border-top: 2px solid #334155;">
                    <td colspan="4" class="text-right">TOTAL AMOUNT PAYABLE TO CONSIGNOR:</td>
                    <td class="text-right" style="color: #d97706;">{{ number_format($totalQuantity) }}</td>
                    <td class="text-right" style="color: #047857; font-size: 13px;">₱{{ number_format($totalPayable, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Accounting Certification & Signatures -->
        <div class="signatures">
            <div class="sign-box">
                <div class="sign-title">Prepared by:</div>
                <div class="sign-line">
                    @if($supplier->consignment_type === 'pharmacy')
                        KRISTINE MAE BATAN
                    @else
                        ALIANA MARIE DULA
                    @endif
                </div>
                <div class="sign-title">
                    @if($supplier->consignment_type === 'pharmacy')
                        Consignment Officer - Pharmacy Stocks
                    @else
                        Consignment Officer - Medical Supplies
                    @endif
                </div>
            </div>
            <div class="sign-box">
                <div class="sign-title">Certified Correct:</div>
                <div class="sign-line">JAHZEILE SALISTRE</div>
                <div class="sign-title">Accountant IV &bull; Head of Accounting</div>
            </div>
            <div class="sign-box">
                <div class="sign-title">Approved for Payment:</div>
                <div class="sign-line">FLORENCIO O. LUCHING JR., M.D., FPCS</div>
                <div class="sign-title">Chief of Hospital II &bull; RMPH</div>
            </div>
        </div>

        <div style="margin-top: 24px; text-align: center; font-size: 10px; color: #94a3b8;">
            This Statement of Consumed Goods serves as the official basis for Disbursement Voucher and check preparation in accordance with COA consignment guidelines.
        </div>
    </div>
</body>
</html>
