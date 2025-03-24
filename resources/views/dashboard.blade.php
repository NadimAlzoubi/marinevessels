<x-app-layout>
    <section class="home-section">
        <div class="text">Dashboard</div>
        @if (session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="color: red;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Responsive Form -->
        <div class="container-fluid mt-2">
            <div class="row" id="vessel-form">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Shipping Form</h5>
                        </div>
                        <div class="card-body">


                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</x-app-layout>
