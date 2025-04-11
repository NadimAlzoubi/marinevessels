<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            {{-- <span>User Details</span> --}}
            <a href="{{ route('fee_categories.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <div class="container-fluid mt-2">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Category Details</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> {{ $category->category_name }}</p>
                            <p><strong>Description:</strong> {{ $category->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>


