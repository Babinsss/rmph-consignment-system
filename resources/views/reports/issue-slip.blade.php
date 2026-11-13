<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Requisition &amp; Issue Slip {{ $requisition->request_number }} - RMPH HECS</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1e293b; margin: 24px; }
        .header { text-align: center; margin-bottom: 18px; border-bottom: 2px solid #0f766e; padding-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0; color: #0f172a; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 9px; color: #475569; }
        .badge-stat { color: #b91c1c; font-weight: bold; }
        .box { border: 1px solid #cbd5e1; border-radius: 4px; padding: 10px; margin-bottom: 14px; background-color: #f8fafc; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: 16px; }
        th { background-color: #f1f5f9; color: #334155; text-align: left; padding: 6px 5px; font-size: 8.5px; border-bottom: 1px solid #cbd5e1; }
        td { padding: 6px 5px; border-bottom: 1px solid #e2e8f0; font-size: 8.5px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .signatures { width: 100%; margin-top: 32px; border: none; }
        .signatures td { border: none; width: 33%; text-align: center; padding-top: 28px; vertical-align: bottom; }
        .sig-line { border-top: 1px solid #334155; margin: 0 14px 4px 14px; }
        .footer { margin-top: 24px; border-top: 1px solid #cbd5e1; padding-top: 6px; font-size: 8px; color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Roxas Memorial Provincial Hospital</h1>
        <p>Arnaldo Boulevard, Roxas City, Capiz, Philippines</p>
        <p><strong>Consignment Requisition &amp; Depot Issue Slip</strong></p>
    </div>

    <div class="box">
        <table style="margin: 0; border: none;">
            <tr>
                <td style="border: none; padding: 2px 0; width: 50%;"><strong>Requisition #:</strong> {{ $requisition->request_number }}</td>
                <td style="border: none; padding: 2px 0; width: 50%;"><strong>Status:</strong> {{ $requisition->status->getLabel() }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;"><strong>Requesting Unit:</strong> {{ $requisition->unit?->name ?? '-' }}</td>
                <td style="border: none; padding: 2px 0;">
                    <strong>Priority:</strong>
                    <span class="{{ $requisition->is_urgent ? 'badge-stat' : '' }}">
                        {{ $requisition->is_urgent ? 'STAT (Urgent)' : 'Regular' }}
                    </span>
                    @if ($requisition->is_downtime)
                        &bull; <strong>Downtime Slip</strong> {{ $requisition->downtime_reference ? "({$requisition->downtime_reference})" : '' }}
                    @endif
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;"><strong>Requested By:</strong> {{ $requisition->requestedBy?->name ?? '-' }}</td>
                <td style="border: none; padding: 2px 0;"><strong>Request Date:</strong> {{ $requisition->request_date?->format('M j, Y • g:i A') ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;"><strong>Fulfilled By:</strong> {{ $requisition->fulfilledBy?->name ?? 'Pending' }}</td>
                <td style="border: none; padding: 2px 0;"><strong>Fulfilled At:</strong> {{ $requisition->fulfilled_at?->format('M j, Y • g:i A') ?? '-' }}</td>
            </tr>
            @if ($requisition->is_urgent && filled($requisition->urgent_reason))
                <tr>
                    <td colspan="2" style="border: none; padding-top: 6px;"><strong>Clinical Reason (STAT):</strong> {{ $requisition->urgent_reason }}</td>
                </tr>
            @endif
            @if (filled($requisition->notes))
                <tr>
                    <td colspan="2" style="border: none; padding-top: 4px;"><strong>Notes:</strong> {{ $requisition->notes }}</td>
                </tr>
            @endif
        </table>
    </div>

    <p style="font-weight: bold; margin-bottom: 4px;">1. Requested &amp; Issued Item Summary</p>
    <table>
        <thead>
            <tr>
                <th style="width: 14%;">Item Code</th>
                <th style="width: 44%;">Item Description</th>
                <th style="width: 16%;">Unit of Measure</th>
                <th style="width: 13%;" class="text-right">Requested</th>
                <th style="width: 13%;" class="text-right">Issued</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requisition->lines as $line)
                <tr>
                    <td><strong>{{ $line->item?->code ?? '-' }}</strong></td>
                    <td>{{ $line->item?->description ?? '-' }}</td>
                    <td>{{ $line->item?->unit_of_measure ?? '-' }}</td>
                    <td class="text-right">{{ number_format($line->quantity_requested) }}</td>
                    <td class="text-right"><strong>{{ number_format($line->quantity_issued) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-weight: bold; margin-bottom: 4px;">2. FEFO Batch &amp; Lot Traceability</p>
    <table>
        <thead>
            <tr>
                <th style="width: 14%;">Item Code</th>
                <th style="width: 34%;">Item Description</th>
                <th style="width: 18%;">Lot / Batch #</th>
                <th style="width: 16%;">Expiration Date</th>
                <th style="width: 18%;" class="text-right">Qty Issued</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($issuedMovements as $movement)
                <tr>
                    <td>{{ $movement->batch?->item?->code ?? '-' }}</td>
                    <td>{{ $movement->batch?->item?->description ?? '-' }}</td>
                    <td><strong>{{ $movement->batch?->lot_number ?? '-' }}</strong></td>
                    <td>{{ $movement->batch?->expiration_date?->format('M j, Y') ?? '-' }}</td>
                    <td class="text-right"><strong>{{ number_format(abs($movement->quantity)) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 14px; color: #64748b;">
                        No depot batches have been issued for this requisition yet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signatures">
        <tr>
            <td>
                <div class="sig-line"></div>
                <strong>Requested By</strong><br>
                <span>{{ $requisition->requestedBy?->name ?? 'Ward Staff' }}</span>
            </td>
            <td>
                <div class="sig-line"></div>
                <strong>Issued By (Depot Custodian)</strong><br>
                <span>{{ $requisition->fulfilledBy?->name ?? 'Stock Custodian' }}</span>
            </td>
            <td>
                <div class="sig-line"></div>
                <strong>Received By (Ward)</strong><br>
                <span>Signature over Printed Name &amp; Date</span>
            </td>
        </tr>
    </table>

    <div class="footer">
        Roxas Memorial Provincial Hospital • Generated {{ $generatedAt }} by {{ $generatedBy }} • Internal Operations Slip (No Pricing Shown)
    </div>
</body>
</html>
