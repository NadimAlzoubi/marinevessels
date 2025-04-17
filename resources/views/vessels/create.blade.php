<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('vessels.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
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
                            <h5 class="mb-0">Add vessel</h5>
                        </div>
                        <div class="card-body">
                            <form id="createVesselForm" action="{{ route('vessels.index') }}" method="POST">
                                @csrf
                                <input type="hidden" id="vessel_id" name="vessel_id">
                                <!-- حقل مخفي لتخزين ID السفينة التي يتم تعديلها -->

                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="client_id" class="form-label">Client</label>
                                        <select name="client_id" id="client_id" class="form-select">
                                            <option value="">--Select Client--</option>
                                            @foreach ($clients as $client)
                                                @if ($client->status == 'active')
                                                    <option value="{{ $client->id }}"
                                                        {{ old('client_id', $vessel->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                                        {{ $client->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid-item">
                                        <label for="vessel_name" class="form-label">Vessel Name</label>
                                        <input type="text" class="form-control" id="vessel_name" name="vessel_name">
                                    </div>

                                    <div class="grid-item">
                                        <label for="port_name" class="form-label">Port Name</label>
                                        <input type="text" class="form-control" id="port_name" name="port_name">
                                    </div>
                                    <div class="grid-item">
                                        <label for="eta" class="form-label">ETA</label>
                                        <input type="datetime-local" class="form-control" id="eta" name="eta">
                                    </div>
                                    <div class="grid-item">
                                        <label for="etd" class="form-label">ETD</label>
                                        <input type="datetime-local" class="form-control" id="etd" name="etd">
                                    </div>
                                    <div class="grid-item">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option selected>--Choose--</option>
                                            <option value="1">Pending</option>
                                            <option value="2">In Progress</option>
                                            <option value="3">Completed</option>
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#client_id').select2({
                placeholder: "--Select Client--",
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: true
            });
        });
    </script>
</x-app-layout>
