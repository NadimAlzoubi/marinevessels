<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
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
        <!-- Responsive Form -->
        <div class="container-fluid mt-2">
            <div class="row" id="vessel-form">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Edit category</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fee_categories.update', $category->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="category_name">Name</label>
                                        <input type="text" name="category_name" id="category_name"
                                            class="form-control" value="{{ $category->category_name }}" required>
                                    </div>
                                    <div class="grid-item">
                                        <label for="description">Description (optional)</label>
                                        <input type="text" name="description" id="description"
                                            class="form-control" value="{{ $category->description }}"></input>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                                    <button type="submit" id="submit-button" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>