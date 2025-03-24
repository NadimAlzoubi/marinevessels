<x-app-layout>
    <section class="home-section">
        <div class="container">
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
        <div class="container mt-2">
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
                                        <label for="vessel_name" class="form-label">Vessel Name</label>
                                        <input type="text" class="form-control" id="vessel_name" name="vessel_name"
                                            placeholder="Enter vessel name">
                                    </div>
                                    <div class="grid-item">
                                        <label for="job_no" class="form-label">Job Number</label>
                                        <input type="text" class="form-control" id="job_no" name="job_no"
                                            placeholder="Enter job number">
                                    </div>
                                    <div class="grid-item">
                                        <label for="port_name" class="form-label">Port Name</label>
                                        <input type="text" class="form-control" id="port_name" name="port_name"
                                            placeholder="Enter port name">
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
</x-app-layout>
