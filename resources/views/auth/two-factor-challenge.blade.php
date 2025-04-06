<section class="home-section">
    <div class="container-fluid">
        <h2>أدخل رمز المصادقة الثنائية</h2>

        <form method="POST" action="{{ route('two-factor.login') }}">
            @csrf
            <label for="two_factor_code">Enter Two-Factor Code</label>
            <input type="text" name="two_factor_code" required autofocus>
            <button type="submit">Verify</button>
        </form>        
    </div>
</section>
