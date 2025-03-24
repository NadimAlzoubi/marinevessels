<x-app-layout>
    <div class="container">
        <h1>الفواتير</h1>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary mb-3">إنشاء فاتورة جديدة</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($invoices->count())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>نوع الفاتورة</th>
                        <th>السفينة</th>
                        <th>تاريخ الفاتورة</th>
                        <th>الإجمالي</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->invoice_type }}</td>
                            <td>{{ $invoice->vessel->vessel_name }}</td>
                            <td>{{ $invoice->invoice_date }}</td>
                            <td>{{ $invoice->grand_total }}</td>
                            <td>
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-info btn-sm">عرض</a>
                                <a href="{{ route('invoices.edit', $invoice->id) }}"
                                    class="btn btn-warning btn-sm">تحرير</a>
                                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('هل أنت متأكد؟')"
                                        class="btn btn-danger btn-sm">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>لا توجد فواتير حتى الآن.</p>
        @endif
    </div>
</x-app-layout>
