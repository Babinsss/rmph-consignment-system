<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consignment Inventory Report ({{ $dateFrom }} to {{ $dateTo }}) - Rizal Medical Center</title>
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
            max-width: 1100px;
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
            margin-right: 8px;
        }

        .hospital-header {
            text-align: center;
            margin-bottom: 24px;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 16px;
        }

        .hospital-header h4 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #718096;
        }

        .hospital-header h2 {
            font-size: 20px;
            font-weight: 800;
            color: #0f766e;
            margin: 4px 0;
        }

        .hospital-header h3 {
            font-size: 14px;
            font-weight: 700;
            color: #2d3748;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hospital-header p {
            font-size: 12px;
            color: #4a5568;
            margin-top: 4px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
            background: #f8fafc;
            padding: 14px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
        }

        .summary-kpi {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .kpi-card {
            border: 1px solid #cbd5e1;
            padding: 12px 16px;
            border-radius: 6px;
            background: #ffffff;
        }

        .kpi-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }

        .kpi-value {
            font-size: 18px;
            font-weight: 800;
            margin-top: 4px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 28px;
        }

        .report-table th, .report-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
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
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
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
                size: landscape;
                margin: 12mm 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Control Bar -->
        <div class="no-print-bar">
            <div>
                <a href="/admin/inventory-reports" class="btn btn-secondary">← Back to Dashboard</a>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-primary">🖨️ Print Report</button>
            </div>
        </div>

        <!-- Official Hospital Header -->
        <div class="hospital-header">
            <h4>Republic of the Philippines • Capiz Provincial Government</h4>
            <h2>ROXAS MEMORIAL PROVINCIAL HOSPITAL</h2>
            <h3>Consignment Inventory Audit & Consumption Summary</h3>
            <p>Roxas City, Capiz • Hospital Extensive Consignment System</p>
        </div>

        <!-- Metadata -->
        <div class="meta-grid">
            <div><strong>Audit Period:</strong> {{ $dateFrom }} to {{ $dateTo }}</div>
            <div><strong>Consignment Stream:</strong> {{ $department === 'all' ? 'All Hospital Streams' : ucfirst(str_replace('_', ' ', $department)) }}</div>
            <div><strong>Generated At:</strong> {{ now()->format('M d, Y h:i A') }}</div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="summary-kpi">
            <div class="kpi-card">
                <div class="kpi-title" style="color: #0d9488;">Period Inwards Received</div>
                <div class="kpi-value" style="color: #0f766e;">₱{{ number_format($totalReceivedCost, 2) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title" style="color: #d97706;">Period Issued / Consumed</div>
                <div class="kpi-value" style="color: #b45309;">₱{{ number_format($totalIssuedCost, 2) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-title" style="color: #059669;">Ending Consignment Valuation</div>
                <div class="kpi-value" style="color: #047857;">₱{{ number_format($totalEndingValuation, 2) }}</div>
            </div>
        </div>

        <!-- Detailed Table -->
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Item Code</th>
                    <th style="width: 20%;">Item & Generic Name</th>
                    <th style="width: 12%;">Stream / Category</th>
                    <th style="width: 6%;">UOM</th>
                    <th style="width: 14%;">Consignor</th>
                    <th class="text-right" style="width: 6%;">Beg.</th>
                    <th class="text-right" style="width: 6%;">+ In</th>
                    <th class="text-right" style="width: 6%;">- Out</th>
                    <th class="text-right" style="width: 6%;">End</th>
                    <th class="text-right" style="width: 7%;">Unit Cost</th>
                    <th class="text-right" style="width: 7%;">Ending Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td style="font-weight: 700;">{{ $row['item_code'] }}</td>
                        <td>
                            <strong>{{ $row['name'] }}</strong>
                            @if($row['generic_name'])
                                <br><small style="color: #64748b;">Generic: {{ $row['generic_name'] }}</small>
                            @endif
                        </td>
                        <td>
                            {{ $row['department'] === 'pharmacy' ? 'Pharmacy' : 'Medical Supply' }}
                            <br><small style="color: #64748b;">{{ $row['category'] }}</small>
                        </td>
                        <td>{{ ucfirst($row['uom']) }}</td>
                        <td>{{ $row['supplier_name'] }}</td>
                        <td class="text-right">{{ $row['beginning_balance'] }}</td>
                        <td class="text-right" style="color: #0f766e; font-weight: 600;">{{ $row['received_qty'] > 0 ? '+'.$row['received_qty'] : '—' }}</td>
                        <td class="text-right" style="color: #b45309; font-weight: 600;">{{ $row['issued_qty'] > 0 ? '-'.$row['issued_qty'] : '—' }}</td>
                        <td class="text-right" style="font-weight: 700;">{{ $row['ending_balance'] }}</td>
                        <td class="text-right">₱{{ number_format($row['unit_cost'], 2) }}</td>
                        <td class="text-right" style="font-weight: 700; color: #047857;">₱{{ number_format($row['ending_valuation'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center" style="padding: 20px; color: #94a3b8;">
                            No consignment inventory movements found for this timeframe.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: 700; border-top: 2px solid #64748b;">
                    <td colspan="6" class="text-right">PERIOD TOTALS:</td>
                    <td class="text-right" style="color: #0f766e;">₱{{ number_format($totalReceivedCost, 2) }}</td>
                    <td class="text-right" style="color: #b45309;">₱{{ number_format($totalIssuedCost, 2) }}</td>
                    <td colspan="2" class="text-right">ENDING VALUATION:</td>
                    <td class="text-right" style="color: #047857;">₱{{ number_format($totalEndingValuation, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Hospital Signatures for Audit Compliance -->
        <div class="signatures">
            <div class="sign-box">
                <div class="sign-title">Prepared by:</div>
                <div class="sign-line">{{ auth()->user()?->name ?? 'Inventory Custodian' }}</div>
                <div class="sign-title">Consignment Inventory Staff</div>
            </div>
            <div class="sign-box">
                <div class="sign-title">Certified Correct:</div>
                <div class="sign-line">Supervising Pharmacist / CSR Head</div>
                <div class="sign-title">Department Supervisor</div>
            </div>
            <div class="sign-box">
                <div class="sign-title">Verified by:</div>
                <div class="sign-line">Materials Management Officer</div>
                <div class="sign-title">Chief of Clinics / Operations</div>
            </div>
            <div class="sign-box">
                <div class="sign-title">Noted & Audited:</div>
                <div class="sign-line">Hospital Accountant</div>
                <div class="sign-title">Commission on Audit (COA)</div>
            </div>
        </div>
    </div>
</body>
</html>
