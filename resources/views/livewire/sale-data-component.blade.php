
<div>
    @if ($sales->count() > 0)
        <table>
            <thead>
                <tr>
                    <th colspan="2">Branch Name</th>
                    <th>Quantity</th>
                    <th>Total Amount</th>
                    <th>Branch Stock Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                    <tr>
                        <td colspan="2">{{ $sale->branch->branch_name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>{{ $sale->total_amount }}</td>
                        <td>{{ $sale->branchStock->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No sales data found.</p>
    @endif
</div>
