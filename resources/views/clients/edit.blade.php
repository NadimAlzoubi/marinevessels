<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('clients.index', $client->id) }}" class="btn btn-secondary mb-3 mt-3">Back</a>
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
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Edit Client</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('clients.update', $client->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" name="name" id="name" value="{{ $client->name }}"
                                            class="form-control" required>
                                    </div>

                                    <div class="grid-item">
                                        <label for="type" class="form-label">Type</label>
                                        <select name="type" id="type" class="form-select" required>
                                            <option value="company" {{ $client->type == 'company' ? 'selected' : '' }}>
                                                Company</option>
                                            <option value="individual"
                                                {{ $client->type == 'individual' ? 'selected' : '' }}>Individual
                                            </option>
                                        </select>
                                    </div>

                                    <div class="grid-item">
                                        <label for="contact_person" class="form-label">Contact Person</label>
                                        <input type="text" name="contact_person" id="contact_person"
                                            value="{{ $client->contact_person }}" class="form-control">
                                    </div>

                                    <div class="grid-item">
                                        <label for="contact_person_phone" class="form-label">Contact Person
                                            Phone</label>
                                        <input type="text" name="contact_person_phone" id="contact_person_phone"
                                            value="{{ $client->contact_person_phone }}" class="form-control">
                                    </div>

                                    <div class="grid-item">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="tel" name="phone" id="phone" value="{{ $client->phone }}"
                                            class="form-control">
                                    </div>

                                    <div class="grid-item">
                                        <label for="fax" class="form-label">Fax</label>
                                        <input type="text" name="fax" id="fax" value="{{ $client->fax }}"
                                            class="form-control">
                                    </div>

                                    <div class="grid-item">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" id="email"
                                            value="{{ $client->email }}" class="form-control">
                                    </div>

                                    <div class="grid-item">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" name="address" id="address"
                                            value="{{ $client->address }}" class="form-control">
                                    </div>

                                    <div class="grid-item">
                                        <label for="country" class="form-label">Country</label>
                                        <select name="country" id="country" class="form-select" required></select>
                                    </div>

                                    <div class="grid-item">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="url" name="website" id="website"
                                            value="{{ $client->website }}" class="form-control">
                                    </div>

                                    <div class="grid-item">
                                        <label for="trn" class="form-label">T.R.N</label>
                                        <input type="text" name="trn" id="trn"
                                            value="{{ $client->trn }}" class="form-control">
                                    </div>

                                    <div class="grid-item">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="active"
                                                {{ $client->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive"
                                                {{ $client->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="grid-item">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ $client->notes }}</textarea>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <a href="{{ route('clients.index') }}"
                                        class="btn btn-outline-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function() {
            $('#country').select2({
                placeholder: '-- Select Country --',
                allowClear: true,
                width: '100%',
                dropdownAutoWidth: true,
                ajax: {
                    url: 'https://restcountries.com/v3.1/all',
                    dataType: 'json',
                    processResults: function(data, params) {
                        const searchTerm = (params.term || '').toLowerCase();

                        const filtered = data
                            .filter(function(country) {
                                return country.name.common.toLowerCase().includes(searchTerm);
                            })
                            .map(function(country) {
                                return {
                                    id: country.name.common,
                                    text: country.name.common
                                };
                            })
                            .sort((a, b) => a.text.localeCompare(b.text));

                        return {
                            results: filtered
                        };
                    }

                }
            });

            // تعيين الدولة الحالية لو موجودة
            @if ($client->country)
                let selectedCountry = @json($client->country);
                let option = new Option(selectedCountry, selectedCountry, true, true);
                $('#country').append(option).trigger('change');
            @endif
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</x-app-layout>
