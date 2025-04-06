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
                            <h5 class="mb-0">Create a new fee category</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fee_categories.store') }}" method="POST">
                                @csrf
                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="category_name">Name</label>
                                        <input type="text" name="category_name" id="category_name"
                                            class="form-control" required>
                                    </div>
                                    <div class="grid-item">
                                        <label for="description">Description (optional)</label>
                                        <input type="text" name="description" id="description" class="form-control"></input>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <div class="container-fluid mt-2">
            <div class="row" id="vessel-form">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">DataTable</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fee_categories.store') }}" method="POST">
                                @csrf
                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="category_name">Name</label>
                                        <input type="text" name="category_name" id="category_name"
                                            class="form-control" required>
                                    </div>
                                    <div class="grid-item">
                                        <label for="description">Description (optional)</label>
                                        <input type="text" name="description" id="description" class="form-control"></input>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</x-app-layout>
