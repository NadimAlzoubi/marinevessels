<x-app-layout>
    <div class="container">
        <h1>تفاصيل فئة الرسوم</h1>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $category->category_name }}</h4>
                <p class="card-text">{{ $category->description }}</p>
                <a href="{{ route('fee_categories.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>
        </div>
    </div>
</x-app-layout>