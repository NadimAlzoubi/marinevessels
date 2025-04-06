<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            {{-- <span>Vessel Details</span> --}}
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

        <div class="container-fluid mt-2">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Vessel Details</h5>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $vessel->vessel_name }}</h5>
                            <p><strong>Job Number:</strong> {{ $vessel->job_no }}</p>
                            <p><strong>Port Name:</strong> {{ $vessel->port_name }}</p>
                            <p><strong>ETA:</strong> {{ \Carbon\Carbon::parse($vessel->eta)->format('d/m/Y h:i A') }}
                            </p>
                            <p><strong>ETD:</strong> {{ \Carbon\Carbon::parse($vessel->etd)->format('d/m/Y h:i A') }}
                            </p>
                            <p><strong>Status:</strong>
                                @if ($vessel->status == 1)
                                    Pending
                                @elseif($vessel->status == 2)
                                    In Progress
                                @elseif($vessel->status == 3)
                                    Completed
                                @endif
                            </p>
                            <p>
                                <a target="_blank" class="btn btn-info me-2"
                                    href="{{ route('pdf.vesselReport.vessel_report', ['id' => $vessel->id, 'clickOption' => 'stream']) }}">
                                    <i class="bx bx-printer"></i> Print report
                                </a>
                                
                                <a class="btn btn-info"
                                    href="{{ route('pdf.vesselReport.vessel_report', ['id' => $vessel->id, 'clickOption' => 'download']) }}">
                                    <i class="bx bx-download"></i> Download report
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Responsive Form -->
        <div class="container-fluid mt-2">
            {{-- <a href="#" class="btn btn-success mb-3" id="add-vessel-button">Add New Vessel +</a> --}}
            <div class="row" id="vessel-form">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Arrival Report Form</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('vessels.update', $vessel->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                @php
                                    $categories = [
                                        'basic' => [
                                            'title' => 'Basic Data',
                                            'fields' => [
                                                (object) [
                                                    'label' => 'Vessel Name',
                                                    'name' => 'vessel_name',
                                                    'type' => 'text',
                                                    'placeholder' => '',
                                                    'value' => $vessel->vessel_name,
                                                ],
                                                // (object) [
                                                //     'label' => 'Job Number',
                                                //     'name' => 'job_no',
                                                //     'type' => 'text',
                                                //     'placeholder' => '',
                                                //     'value' => $vessel->job_no,
                                                // ],
                                                (object) [
                                                    'label' => 'Port Name',
                                                    'name' => 'port_name',
                                                    'type' => 'text',
                                                    'placeholder' => '',
                                                    'value' => $vessel->port_name,
                                                ],
                                                (object) [
                                                    'label' => 'Berth No',
                                                    'name' => 'berth_no',
                                                    'type' => 'text',
                                                    'placeholder' => '',
                                                    'value' => $vessel->berth_no,
                                                ],
                                                (object) [
                                                    'label' => 'Voyage',
                                                    'name' => 'voy',
                                                    'type' => 'text',
                                                    'placeholder' => '',
                                                    'value' => $vessel->voy,
                                                ],
                                                (object) [
                                                    'label' => 'ETA',
                                                    'name' => 'eta',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->eta,
                                                ],
                                                (object) [
                                                    'label' => 'ETD',
                                                    'name' => 'etd',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->etd,
                                                ],
                                                (object) [
                                                    'label' => 'Gross Register Tonnage',
                                                    'name' => 'grt',
                                                    'type' => 'text',
                                                    'placeholder' => '',
                                                    'value' => $vessel->grt,
                                                ],
                                                (object) [
                                                    'label' => 'Net Register Tonnage',
                                                    'name' => 'nrt',
                                                    'type' => 'text',
                                                    'placeholder' => '',
                                                    'value' => $vessel->nrt,
                                                ],
                                                (object) [
                                                    'label' => 'Deadweight Tonnage',
                                                    'name' => 'dwt',
                                                    'type' => 'text',
                                                    'placeholder' => '',
                                                    'value' => $vessel->dwt,
                                                ],
                                                (object) [
                                                    'label' => 'Status',
                                                    'name' => 'status',
                                                    'type' => 'text',
                                                    'placeholder' => '',
                                                    'value' => $vessel->status,
                                                ],
                                            ],
                                        ],
                                        'timeline' => [
                                            'title' => 'Timeline',
                                            'fields' => [
                                                (object) [
                                                    'label' => 'End of Sea Passage Time',
                                                    'name' => 'eosp',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->eosp,
                                                ],
                                                (object) [
                                                    'label' => 'Arrived Abu Dhabi On',
                                                    'name' => 'aado',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->aado,
                                                ],
                                                (object) [
                                                    'label' => 'NOR Tendered On',
                                                    'name' => 'nor_tendered',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->nor_tendered,
                                                ],
                                                (object) [
                                                    'label' => 'NOR Accepted On',
                                                    'name' => 'nor_accepted',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->nor_accepted,
                                                ],
                                                (object) [
                                                    'label' => 'Dropped Anchor',
                                                    'name' => 'dropped_anchor',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->dropped_anchor,
                                                ],
                                                (object) [
                                                    'label' => 'Heaved Up Anchor',
                                                    'name' => 'heaved_up_anchor',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->heaved_up_anchor,
                                                ],
                                                (object) [
                                                    'label' => 'Pilot Boarded On',
                                                    'name' => 'pilot_boarded',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->pilot_boarded,
                                                ],
                                                (object) [
                                                    'label' => 'First Line',
                                                    'name' => 'first_line',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->first_line,
                                                ],
                                                (object) [
                                                    'label' => 'Berthed On',
                                                    'name' => 'berthed_on',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->berthed_on,
                                                ],
                                                (object) [
                                                    'label' => 'Made Fast On',
                                                    'name' => 'made_fast',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->made_fast,
                                                ],
                                                (object) [
                                                    'label' => 'Sailed On',
                                                    'name' => 'sailed_on',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->sailed_on,
                                                ],
                                            ],
                                        ],
                                        'bunkers_on_arrival' => [
                                            'title' => 'Bunkers ROB (On Arrival)',
                                            'fields' => [
                                                (object) [
                                                    'label' => 'Fuel Oil',
                                                    'name' => 'arrival_fuel_oil',
                                                    'type' => 'text',
                                                    'placeholder' => '(Tons)',
                                                    'value' => $vessel->arrival_fuel_oil,
                                                ],
                                                (object) [
                                                    'label' => 'Diesel Oil',
                                                    'name' => 'arrival_diesel_oil',
                                                    'type' => 'text',
                                                    'placeholder' => '(Tons)',
                                                    'value' => $vessel->arrival_diesel_oil,
                                                ],
                                                (object) [
                                                    'label' => 'Fresh Water',
                                                    'name' => 'arrival_fresh_water',
                                                    'type' => 'text',
                                                    'placeholder' => '(Tons)',
                                                    'value' => $vessel->arrival_fresh_water,
                                                ],
                                                (object) [
                                                    'label' => 'Draft FWD',
                                                    'name' => 'arrival_draft_fwd',
                                                    'type' => 'text',
                                                    'placeholder' => '(Meters)',
                                                    'value' => $vessel->arrival_draft_fwd,
                                                ],
                                                (object) [
                                                    'label' => 'Draft AFT',
                                                    'name' => 'arrival_draft_aft',
                                                    'type' => 'text',
                                                    'placeholder' => '(Meters)',
                                                    'value' => $vessel->arrival_draft_aft,
                                                ],
                                            ],
                                        ],
                                        'bunkers_on_departure' => [
                                            'title' => 'Bunkers ROB (On Departure)',
                                            'fields' => [
                                                (object) [
                                                    'label' => 'Fuel Oil',
                                                    'name' => 'departure_fuel_oil',
                                                    'type' => 'text',
                                                    'placeholder' => '(Tons)',
                                                    'value' => $vessel->departure_fuel_oil,
                                                ],
                                                (object) [
                                                    'label' => 'Diesel Oil',
                                                    'name' => 'departure_diesel_oil',
                                                    'type' => 'text',
                                                    'placeholder' => '(Tons)',
                                                    'value' => $vessel->departure_diesel_oil,
                                                ],
                                                (object) [
                                                    'label' => 'Fresh Water',
                                                    'name' => 'departure_fresh_water',
                                                    'type' => 'text',
                                                    'placeholder' => '(Tons)',
                                                    'value' => $vessel->departure_fresh_water,
                                                ],
                                                (object) [
                                                    'label' => 'Draft FWD',
                                                    'name' => 'departure_draft_fwd',
                                                    'type' => 'text',
                                                    'placeholder' => '(Meters)',
                                                    'value' => $vessel->departure_draft_fwd,
                                                ],
                                                (object) [
                                                    'label' => 'Draft AFT',
                                                    'name' => 'departure_draft_aft',
                                                    'type' => 'text',
                                                    'placeholder' => '(Meters)',
                                                    'value' => $vessel->departure_draft_aft,
                                                ],
                                            ],
                                        ],
                                        'extra' => [
                                            'title' => 'Extra',
                                            'fields' => [
                                                (object) [
                                                    'label' => 'Next Port of Call',
                                                    'name' => 'next_port_of_call',
                                                    'type' => 'text',
                                                    'placeholder' => '',
                                                    'value' => $vessel->next_port_of_call,
                                                ],
                                                (object) [
                                                    'label' => 'ETA for Next Port',
                                                    'name' => 'eta_next_port',
                                                    'type' => 'datetime-local',
                                                    'placeholder' => '',
                                                    'value' => $vessel->eta_next_port,
                                                ],
                                                (object) [
                                                    'label' => 'Any Requirements',
                                                    'name' => 'any_requirements',
                                                    'type' => 'textarea',
                                                    'placeholder' => '',
                                                    'value' => $vessel->any_requirements,
                                                ],
                                            ],
                                        ],
                                    ];
                                @endphp


                                @foreach ($categories as $category)
                                    <h5 class="mt-5">{{ $category['title'] }}:</h5> <!-- عنوان الفئة -->
                                    <div class="grid-container">
                                        @foreach ($category['fields'] as $field)
                                            <div class="grid-item">
                                                <label for="{{ $field->name }}"
                                                    class="form-label">{{ $field->label }}</label>
                                                <input value="{{ $field->value }}" type="{{ $field->type }}"
                                                    class="form-control" id="{{ $field->name }}"
                                                    name="{{ $field->name }}"
                                                    placeholder="{{ $field->placeholder }}">
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach

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
