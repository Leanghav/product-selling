
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #777;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #eeeeee;
        }

        .number {
            text-align: right;
        }
    </style>
</head>
<body>

    <h2>Sales Report</h2>

    <p>
        Period: {{ $from }} to {{ $to }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Paid</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td>{{ $sale->invoice_no }}</td>
                    <td>{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                    <td>{{ $sale->created_at?->format('Y-m-d') }}</td>
                    <td class="number">
                        {{ number_format($sale->total, 2) }}
                    </td>
                    <td class="number">
                        {{ number_format($sale->paid, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No sales found.</td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr>
                <th colspan="3">Total</th>
                <th>{{ number_format($total, 2) }}</th>
                <th>{{ number_format($paid, 2) }}</th>
            </tr>
        </tfoot>
    </table>

</body>
</html>
