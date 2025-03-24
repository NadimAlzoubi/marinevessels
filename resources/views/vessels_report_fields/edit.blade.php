<x-app-layout>
    <section class="home-section">
        <div class="text">Vessels</div>
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
        <div class="container mt-2">
            <div class="row" id="vessel-form">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Shipping Form</h5>
                        </div>
                        <div class="card-body">

                            <form id="add-vessel-report-fields">
                                @csrf
                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="label" class="form-label">Field Label</label>
                                        <input type="text" class="form-control" id="label" name="label"
                                            placeholder="Enter field label">
                                    </div>
                                    <div class="grid-item">
                                        <label for="name" class="form-label">Field Name (Use _)</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter field name">
                                    </div>
                                    <div class="grid-item">
                                        <label for="type" class="form-label">Field Type</label>
                                        <select id="type" name="type" class="form-select">
                                            <option value="text">Text</option>
                                            <option value="number">Number</option>
                                            <option value="date">Date</option>
                                        </select>
                                    </div>
                                    <div class="grid-item">
                                        <label for="placeholder" class="form-label">Placeholder</label>
                                        <input type="text" class="form-control" id="placeholder" name="placeholder"
                                            placeholder="Enter placeholder">
                                    </div>
                                    <div class="grid-item">
                                        <label for="category" class="form-label">Category</label>
                                        <input type="text" class="form-control" id="category" name="category"
                                            placeholder="Enter category">
                                    </div>

                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <a href="{{ route('vessels.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                                    <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
