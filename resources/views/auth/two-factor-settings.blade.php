<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <h2>إعدادات المصادقة الثنائية</h2>
            @if (auth()->user()->two_factor_secret)
                <p>المصادقة الثنائية مفعّلة.</p>
                <form method="POST" action="{{ url('user/two-factor-authentication') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit">تعطيل المصادقة الثنائية</button>
                </form>
            @else
                <p>المصادقة الثنائية غير مفعّلة.</p>
                <form method="POST" action="{{ url('user/two-factor-authentication') }}">
                    @csrf
                    <button type="submit">تفعيل المصادقة الثنائية</button>
                </form>
            @endif
        </div>
    </section>
</x-app-layout>
