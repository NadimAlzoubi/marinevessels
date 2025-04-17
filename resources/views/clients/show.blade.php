<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('clients.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
        </div>

        <div class="container-fluid mt-2">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-header bg-primary text-white rounded-t-lg">
                            <h1 class="mb-0">Client Details</h1>
                        </div>
                        <div class="card-body">
                            <div class="p-4 bg-gray-100 rounded-lg shadow-inner">
                                <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ $client->name }}</h2>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <p><strong class="text-blue-600">Website:</strong> <a
                                            href="{{ $client->website }}" target="_blank"
                                            class="text-blue-500 hover:underline">{{ $client->website }}</a></p>
                                        <p><strong class="text-blue-600">Email:</strong> {{ $client->email }}</p>
                                        <p><strong class="text-blue-600">Phone:</strong> {{ $client->phone }}</p>
                                        <p><strong class="text-blue-600">Fax:</strong> {{ $client->fax }}</p>
                                        <p><strong class="text-blue-600">TRN:</strong> {{ $client->trn }}</p>
                                    </div>
                                    <div>
                                        <p><strong class="text-blue-600">Country:</strong> {{ $client->country }}</p>

                                        <p><strong class="text-blue-600">Address:</strong> {{ $client->address }}</p>
                                        <p><strong class="text-blue-600">Contact Person:</strong>
                                            {{ $client->contact_person }} | {{ $client->contact_person_phone }}</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <p><strong class="text-blue-600">Status:</strong>
                                        @php
                                            $statuses = [
                                                'inactive' =>
                                                    '<span class="badge bg-warning text-dark">Inactive</span>',
                                                'active' => '<span class="badge bg-success text-white">Active</span>',
                                            ];

                                            echo $statuses[$client->status] ??
                                                '<span class="badge bg-secondary">Unknown</span>';
                                        @endphp
                                    </p>

                                    <p><strong class="text-blue-600">Type:</strong> {{ $client->type }}</p>
                                    <p><strong class="text-blue-600">Notes:</strong> {{ $client->notes }}</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">Edit Client</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
