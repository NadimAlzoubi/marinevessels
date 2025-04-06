<x-app-layout>
    <div class="container-fluid">
        <h1>تحرير فئة الرسوم</h1>
        <form action="{{ route('fee_categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="category_name">اسم الفئة</label>
                <input type="text" name="category_name" id="category_name" class="form-control"
                    value="{{ $category->category_name }}" required>
            </div>
            <div class="form-group">
                <label for="description">الوصف (اختياري)</label>
                <textarea name="description" id="description" class="form-control">{{ $category->description }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-3">تحديث</button>
        </form>
    </div>
</x-app-layout>
