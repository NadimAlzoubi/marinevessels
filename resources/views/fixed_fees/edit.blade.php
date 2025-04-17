<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('fixed_fees.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
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
                            <h5 class="mb-0">Update fixed fee</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('fixed_fees.update', $fixedFee->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="fee_category_id">Fee category</label>
                                        <select name="fee_category_id" id="fee_category_id" class="form-control"
                                            required>
                                            <option value="">-- Select --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $fixedFee->fee_category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid-item">
                                        <label for="fee_name">Name</label>
                                        <input type="text" name="fee_name" id="fee_name" class="form-control"
                                            value="{{ $fixedFee->fee_name }}" required>
                                    </div>
                                    <div class="grid-item">
                                        <label for="description">Description</label>
                                        <input type="text" name="description" id="description"
                                            value="{{ $fixedFee->description }}" class="form-control"></input>
                                    </div>
                                    <div class="grid-item">
                                        <label for="amount">Amount</label>
                                        <input type="number" min="0" step="0.001" name="amount"
                                            value="{{ $fixedFee->amount }}" id="amount" class="form-control"></input>
                                    </div>
                                    <div class="grid-item">
                                        <label for="fee_category_id">Pricing rule</label>
                                        <select name="pricing_rule" class="form-select" required>
                                            <option value="">-- Select --</option>
                                            <option value="quantity" {{ $fixedFee->pricing_rule == 'quantity' ? 'selected' : '' }}>By Quantity</option>
                                            <option value="fixed" {{ $fixedFee->pricing_rule == 'fixed' ? 'selected' : '' }}>Fixed (1)</option>
                                            <option value="loa" {{ $fixedFee->pricing_rule == 'loa' ? 'selected' : '' }}>By Length (LOA)</option>
                                            <option value="gt" {{ $fixedFee->pricing_rule == 'gt' ? 'selected' : '' }}>By Gross Tonnage (GT)</option>
                                            <option value="time" {{ $fixedFee->pricing_rule == 'time' ? 'selected' : '' }}>By Hour</option>
                                            <option value="day" {{ $fixedFee->pricing_rule == 'day' ? 'selected' : '' }}>By Day</option>
                                            <option value="percentage" {{ $fixedFee->pricing_rule == 'percentage' ? 'selected' : '' }}>By Percentage</option>
                                        </select>
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