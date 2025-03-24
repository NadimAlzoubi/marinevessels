<x-app-layout>
    <div class="container">
        <h1>تفاصيل الرسم الثابت</h1>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $fixedFee->fee_name }}</h4>
                <p class="card-text"><strong>الوصف:</strong> {{ $fixedFee->description }}</p>
                <p class="card-text"><strong>المبلغ:</strong> {{ $fixedFee->amount }}</p>
                <p class="card-text"><strong>نسبة الضريبة:</strong> {{ $fixedFee->tax_rate * 100 }}%</p>
                <p class="card-text"><strong>فئة الرسم:</strong> {{ $fixedFee->feeCategory->category_name }}</p>
                <a href="{{ route('fixed_fees.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>
        </div>
    </div>
</x-app-layout>
