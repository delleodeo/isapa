<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SpnrCo Sales Report</title>
    <style>
        @page { margin: 15mm; }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #0F172A;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #1D4ED8;
        }
        .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #1D4ED8;
            margin: 0;
        }
        .report-title {
            font-size: 14pt;
            color: #374151;
            margin: 5px 0;
        }
        .report-period {
            font-size: 10pt;
            color: #6B7280;
            margin: 5px 0;
        }
        .info-section {
            background: #F1F5F9;
            padding: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #1D4ED8;
        }
        .info-section h3 {
            margin: 0 0 8px 0;
            font-size: 12pt;
            color: #1D4ED8;
        }
        .info-section p {
            margin: 3px 0;
            font-size: 9pt;
            color: #374151;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #1D4ED8;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 9pt;
        }
        tr:nth-child(even) {
            background: #F9FAFB;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row {
            background: #DBEAFE !important;
            font-weight: bold;
            border-top: 2px solid #1D4ED8;
            border-bottom: 2px solid #1D4ED8;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            font-size: 8pt;
            color: #9CA3AF;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #9CA3AF;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="logo">SpnrCo</h1>
        <p class="report-title">Sales Report</p>
        <p class="report-period">{{ $period }} - {{ $dateRange }}</p>
    </div>

    <div class="info-section">
        <h3>{{ $shop->shop_name }}</h3>
        <p><strong>Address:</strong> {{ $shop->address }}</p>
        <p><strong>Contact:</strong> {{ $shop->contact_number }}</p>
        <p><strong>Total Orders:</strong> {{ $totalOrders }} | <strong>Shop Revenue (90%):</strong> {{ number_format($totalRevenue, 2) }}</p>
    </div>

    @if($orders->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Tracking Number</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th class="text-center">Weight (kg)</th>
                    <th class="text-right">Total Amount</th>
                    <th class="text-right">Shop Revenue</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalAmount = 0;
                @endphp
                @foreach($orders as $order)
                    @php
                        $amount = $order->final_amount ?? $order->total_amount;
                        $totalAmount += $amount;
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($order->updated_at)->format('M d, Y') }}</td>
                        <td>{{ $order->tracking_number }}</td>
                        <td>{{ $order->customer->name }}</td>
                        <td>{{ $order->service->service_name }}</td>
                        <td class="text-center">{{ $order->weight_kg ? number_format($order->weight_kg, 2) : '-' }}</td>
                        <td class="text-right">{{ number_format($amount, 2) }}</td>
                        <td class="text-right">{{ number_format($order->final_shop_revenue ?? $order->shop_revenue, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalRevenue, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="no-data">No orders found for this period</div>
    @endif

    <div class="footer">
        <p>Generated on {{ $generatedAt }} | SpnrCo Laundry Booking System</p>
        <p>Note: Shop revenue is 90% of total amount. Platform fee (10%) is deducted.</p>
    </div>
</body>
</html>
