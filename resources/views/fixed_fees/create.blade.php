<x-app-layout>
    <div class="container-fluid">
        <h1>إضافة رسم ثابت جديد</h1>
        <form action="{{ route('fixed_fees.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="fee_name">اسم الرسم</label>
                <input type="text" name="fee_name" id="fee_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">الوصف (اختياري)</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label for="amount">المبلغ</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tax_rate">نسبة الضريبة (مثال: 0.05 لــ 5%)</label>
                <input type="number" step="0.0001" name="tax_rate" id="tax_rate" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="fee_category_id">فئة الرسم</label>
                <select name="fee_category_id" id="fee_category_id" class="form-control" required>
                    <option value="">اختر فئة</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">حفظ</button>
        </form>
    </div>
</x-app-layout>
