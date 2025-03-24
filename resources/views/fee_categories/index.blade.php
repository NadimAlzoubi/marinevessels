<x-app-layout>
    <div class="container">
        <h1>فئات الرسوم</h1>
        <a href="{{ route('fee_categories.create') }}" class="btn btn-primary mb-3">إنشاء فئة جديدة</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($categories->count())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>اسم الفئة</th>
                        <th>الوصف</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->category_name }}</td>
                            <td>{{ $category->description }}</td>
                            <td>
                                <a href="{{ route('fee_categories.show', $category->id) }}"
                                    class="btn btn-info btn-sm">عرض</a>
                                <a href="{{ route('fee_categories.edit', $category->id) }}"
                                    class="btn btn-warning btn-sm">تحرير</a>
                                <form action="{{ route('fee_categories.destroy', $category->id) }}" method="POST"
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
            <p>لا توجد فئات رسوم بعد.</p>
        @endif
    </div>
</x-app-layout>
