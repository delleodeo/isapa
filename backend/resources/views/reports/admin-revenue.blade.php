<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SpnrCo Revenue Report</title>
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
            border-bottom: 3px solid #1E3A8A;
        }
        .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #1E3A8A;
            margin: 0;
        }
        .admin-badge {
            background: #1E3A8A;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
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
        .summary-section {
            background: #F9FAFB;
            padding: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #1E3A8A;
        }
        .summary-section p {
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
            background: #1E3A8A;
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
            border-top: 2px solid #1E3A8A;
            border-bottom: 2px solid #1E3A8A;
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
        <span class="admin-badge">ADMIN</span>
        <h1 class="logo">SpnrCo</h1>
        <p class="report-title">Revenue Report</p>
        <p class="report-period">{{ $period }} - {{ $dateRange }}</p>
    </div>

    <div class="summary-section">
        <p><strong>Total Orders:</strong> {{ $totalOrders }}</p>
        <p><strong>Platform Revenue (10%):</strong> {{ number_format($totalAdminRevenue, 2) }}</p>
        <p><strong>Shop Revenue (90%):</strong> {{ number_format($totalShopRevenue, 2) }}</p>
        <p><strong>Total Amount:</strong> {{ number_format($totalRevenue, 2) }}</p>
    </div>

    @if($shopBreakdown->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Shop Name</th>
                    <th class="text-center">Orders</th>
                    <th class="text-right">Platform Fee (10%)</th>
                    <th class="text-right">Shop Earnings (90%)</th>
                    <th class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shopBreakdown as $shop)
                    <tr>
                        <td>{{ $shop['shop_name'] }}</td>
                        <td class="text-center">{{ $shop['orders_count'] }}</td>
                        <td class="text-right">{{ number_format($shop['admin_revenue'], 2) }}</td>
                        <td class="text-right">{{ number_format($shop['shop_revenue'], 2) }}</td>
                        <td class="text-right">{{ number_format($shop['total_revenue'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>{{ $totalOrders }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalAdminRevenue, 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalShopRevenue, 2) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($totalRevenue, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="no-data">No revenue data found for this period</div>
    @endif

    <div class="footer">
        <p>Generated on {{ $generatedAt }} | SpnrCo Laundry Booking System</p>
        <p>Platform commission: 10% | Shop earnings: 90% of total order amount</p>
    </div>
</body>
</html>
