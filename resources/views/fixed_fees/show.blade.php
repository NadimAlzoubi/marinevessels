<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('fixed_fees.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
        </div>
        <div class="container-fluid mt-2">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm border-0 rounded-lg">
                        <div class="card-header bg-primary text-white rounded-t-lg">
                            <h1 class="mb-0">Fixed fee Details</h1>
                        </div>
                        <div class="card-body">
                            <div class="p-4 bg-gray-100 rounded-lg shadow-inner">
                                <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ ucfirst($fixedFee->fee_name) }}
                                </h2>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <p class="mb-2"><strong class="text-blue-600">Fee Category:</strong>
                                            {{ ucfirst($fixedFee->feeCategory->category_name) }}</p>
                                        <p class="mb-2"><strong class="text-blue-600">Description:</strong>
                                            {{ ucfirst($fixedFee->description) }}</p>
                                        <p class="mb-2"><strong class="text-blue-600">Amount:</strong> {{ $fixedFee->amount }}</p>
                                        <p class="mb-2"><strong class="text-blue-600">Pricing Rule:</strong>
                                            @php
                                                switch ($fixedFee->pricing_rule) {
                                                    case 'fixed':
                                                        echo 'Fixed (1)';
                                                        break;
                                                    case 'loa':
                                                        echo 'By Length (LOA)';
                                                        break;
                                                    case 'gt':
                                                        echo 'By Gross Tonnage (GT)';
                                                        break;
                                                    case 'time':
                                                        echo 'By Hour';
                                                        break;
                                                    case 'day':
                                                        echo 'By Day';
                                                        break;
                                                    case 'quantity':
                                                        echo 'By Quantity';
                                                        break;
                                                    case 'percentage':
                                                        echo 'By Percentage';
                                                        break;
                                                    default:
                                                        echo 'Unknown';
                                                }
                                            @endphp
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('fixed_fees.edit', $fixedFee) }}" class="btn btn-warning">Edit Fixed
                                    fee</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
