<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
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
        <!-- Edit Vessel Form -->
        <div class="container-fluid mt-2">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Edit Vessel</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('vessels.update', $vessel->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="job_no" class="form-label">Job Number</label>
                                        <input disabled type="text" class="form-control" id="job_no" name="job_no"
                                            value="{{ $vessel->job_no }}" placeholder="Enter job number">
                                    </div>
                                    <div class="grid-item">
                                        <label for="vessel_name" class="form-label">Vessel Name</label>
                                        <input type="text" class="form-control" id="vessel_name" name="vessel_name"
                                            value="{{ $vessel->vessel_name }}" placeholder="Enter vessel name">
                                    </div>
                                    <div class="grid-item">
                                        <label for="port_name" class="form-label">Port Name</label>
                                        <input type="text" class="form-control" id="port_name" name="port_name"
                                            value="{{ $vessel->port_name }}" placeholder="Enter port name">
                                    </div>
                                    <div class="grid-item">
                                        <label for="eta" class="form-label">ETA</label>
                                        <input type="datetime-local" class="form-control" id="eta" name="eta"
                                            value="{{ \Carbon\Carbon::parse($vessel->eta)->format('Y-m-d\TH:i') }}">
                                    </div>
                                    <div class="grid-item">
                                        <label for="etd" class="form-label">ETD</label>
                                        <input type="datetime-local" class="form-control" id="etd" name="etd"
                                            value="{{ \Carbon\Carbon::parse($vessel->etd)->format('Y-m-d\TH:i') }}">
                                    </div>
                                    <div class="grid-item">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="1" {{ $vessel->status == 1 ? 'selected' : '' }}>Pending</option>
                                            <option value="2" {{ $vessel->status == 2 ? 'selected' : '' }}>In Progress</option>
                                            <option value="3" {{ $vessel->status == 3 ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <a href="{{ route('vessels.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
