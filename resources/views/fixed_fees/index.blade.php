<x-app-layout>
    <div class="container">
        <h1>الرسوم الثابتة</h1>
        <a href="{{ route('fixed_fees.create') }}" class="btn btn-primary mb-3">إضافة رسم ثابت جديد</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($fixedFees->count())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>اسم الرسم</th>
                        <th>الوصف</th>
                        <th>المبلغ</th>
                        <th>نسبة الضريبة</th>
                        <th>فئة الرسم</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fixedFees as $fee)
                        <tr>
                            <td>{{ $fee->id }}</td>
                            <td>{{ $fee->fee_name }}</td>
                            <td>{{ $fee->description }}</td>
                            <td>{{ $fee->amount }}</td>
                            <td>{{ $fee->tax_rate * 100 }}%</td>
                            <td>{{ $fee->feeCategory->category_name }}</td>
                            <td>
                                <a href="{{ route('fixed_fees.show', $fee->id) }}" class="btn btn-info btn-sm">عرض</a>
                                <a href="{{ route('fixed_fees.edit', $fee->id) }}"
                                    class="btn btn-warning btn-sm">تحرير</a>
                                <form action="{{ route('fixed_fees.destroy', $fee->id) }}" method="POST"
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
            <p>لا توجد رسوم ثابتة بعد.</p>
        @endif
    </div>
</x-app-layout>
