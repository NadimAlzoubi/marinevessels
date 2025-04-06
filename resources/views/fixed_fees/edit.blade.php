<x-app-layout>
    <div class="container-fluid">
        <h1>تحرير الرسم الثابت</h1>
        <form action="{{ route('fixed_fees.update', $fixedFee->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="fee_name">اسم الرسم</label>
                <input type="text" name="fee_name" id="fee_name" class="form-control" value="{{ $fixedFee->fee_name }}"
                    required>
            </div>
            <div class="form-group">
                <label for="description">الوصف (اختياري)</label>
                <textarea name="description" id="description" class="form-control">{{ $fixedFee->description }}</textarea>
            </div>
            <div class="form-group">
                <label for="amount">المبلغ</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                    value="{{ $fixedFee->amount }}" required>
            </div>
            <div class="form-group">
                <label for="tax_rate">نسبة الضريبة</label>
                <input type="number" step="0.0001" name="tax_rate" id="tax_rate" class="form-control"
                    value="{{ $fixedFee->tax_rate }}" required>
            </div>
            <div class="form-group">
                <label for="fee_category_id">فئة الرسم</label>
                <select name="fee_category_id" id="fee_category_id" class="form-control" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $fixedFee->fee_category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">تحديث</button>
        </form>
    </div>
</x-app-layout>
