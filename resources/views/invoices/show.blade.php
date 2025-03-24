<x-app-layout>

    <div class="container">
        <h1>تفاصيل الفاتورة</h1>
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="card-title">رقم الفاتورة: {{ $invoice->invoice_number }}</h4>
                <p class="card-text"><strong>نوع الفاتورة:</strong> {{ $invoice->invoice_type }}</p>
                <p class="card-text"><strong>السفينة:</strong> {{ $invoice->vessel->vessel_name }}</p>
                <p class="card-text"><strong>تاريخ الفاتورة:</strong> {{ $invoice->invoice_date }}</p>
                <p class="card-text"><strong>المجموع الفرعي:</strong> {{ $invoice->sub_total }}</p>
                <p class="card-text"><strong>الضريبة:</strong> {{ $invoice->tax_total }}</p>
                <p class="card-text"><strong>الإجمالي:</strong> {{ $invoice->grand_total }}</p>
            </div>
        </div>

        <h3>الرسوم المضافة:</h3>
        @if ($invoice->fixedFees->count())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>اسم الرسم</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->fixedFees as $fee)
                        <tr>
                            <td>{{ $fee->fee_name }}</td>
                            <td>{{ $fee->pivot->quantity }}</td>
                            <td>{{ $fee->amount }}</td>
                            <td>{{ $fee->amount * $fee->pivot->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>لم تتم إضافة رسوم لهذه الفاتورة.</p>
        @endif

        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">العودة للقائمة</a>
    </div>
</x-app-layout>