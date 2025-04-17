<x-app-layout>
    <section class="home-section">
        <div class="container py-4">
            {{-- <h2 class="mb-4">Service Details</h2> --}}

            {{-- Service Basic Info --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Service Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Service Number:</strong> {{ $invoice->invoice_number }}</div>
                        <div class="col-md-4"><strong>Service Type:</strong> {{ ucfirst($invoice->invoice_type) }}</div>
                        <div class="col-md-4"><strong>Service Date:</strong> {{ $invoice->invoice_date }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Currency:</strong> {{ $invoice->currency }}</div>
                        <div class="col-md-4"><strong>Vessel:</strong>
                            {{ ucfirst($invoice->vessel->vessel_name) ?? '-' }}</div>
                        <div class="col-md-4"><strong>Voyage:</strong> {{ ucfirst($invoice->vessel->voy) ?? '-' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>NRT:</strong> {{ $invoice->vessel->nrt }}</div>
                        <div class="col-md-4"><strong>GRT:</strong> {{ $invoice->vessel->grt }}</div>
                        <div class="col-md-4"><strong>Port:</strong> {{ ucfirst($invoice->vessel->port_name) ?? '-' }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>ETA:</strong>
                            {{ \Carbon\Carbon::parse($invoice->vessel->eta)->format('d/m/Y h:i A') }}</div>
                        <div class="col-md-4"><strong>ETD:</strong>
                            {{ \Carbon\Carbon::parse($invoice->vessel->etd)->format('d/m/Y h:i A') }}</div>
                        <div class="col-md-4"><strong>Status:</strong>
                            @php
                                $statuses = [
                                    1 => '<span class="badge bg-warning text-dark">Pending</span>',
                                    2 => '<span class="badge bg-info text-white">In Progress</span>',
                                    3 => '<span class="badge bg-success text-white">Completed</span>',
                                ];
                            @endphp

                            {!! $statuses[$invoice->vessel->status] ?? '<span class="badge bg-secondary">Unknown</span>' !!}

                        </div>
                    </div>
                </div>
            </div>

            {{-- Fees Table --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Charges</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered m-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fee Name</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Amount</th>
                                <th>Discount Rate (%)</th>
                                <th>Discount Amount</th>
                                <th>Sub Amount</th>
                                <th>Tax Rate (%)</th>
                                <th>Tax Amount</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $subTotal = 0;
                                $taxTotal = 0;
                                $groupedFees = $invoice->fees->groupBy('feeCategory.category_name');
                            @endphp

                            @forelse ($groupedFees as $categoryName => $fees)
                                <tr class="table-group-divider bg-light">
                                    <td colspan="11" class="fw-bold text-primary">
                                        {{ $categoryName ?? 'Uncategorized' }}</td>
                                </tr>

                                @foreach ($fees as $fee)
                                    @php
                                        $quantity = $fee->pivot->quantity;
                                        $discount = $fee->pivot->discount;
                                        $price = $fee->pivot->amount;
                                        $taxRate = $fee->pivot->tax_rate;

                                        $line = $price * $quantity;
                                        $discountAmount = $line * ($discount / 100);
                                        $lineAfterDiscount = $line - $discountAmount;
                                        $taxAmount = $lineAfterDiscount * ($taxRate / 100);
                                        $lineTotal = $lineAfterDiscount + $taxAmount;

                                        $subTotal += $lineAfterDiscount;
                                        $taxTotal += $taxAmount;
                                    @endphp

                                    <tr>
                                        <td>{{ $fee->fee_name }}</td>
                                        <td>{{ $fee->pivot->description }}</td>
                                        <td>{{ $quantity }}</td>
                                        <td>${{ number_format($price, 3) }}</td>
                                        <td>${{ number_format($line, 3) }}</td>
                                        <td>{{ $discount }}%</td>
                                        <td>${{ number_format($discountAmount, 3) }}</td>
                                        <td>${{ number_format($lineAfterDiscount, 3) }}</td>
                                        <td>{{ $taxRate }}%</td>
                                        <td>${{ number_format($taxAmount, 3) }}</td>
                                        <td>${{ number_format($lineTotal, 3) }}</td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">No fees added to this invoice.</td>
                                </tr>
                            @endforelse
                        </tbody>

                        {{-- Totals --}}
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="8" class="text-end fw-bold">Sub Total:</td>
                                <td colspan="3" class="text-end">${{ number_format($subTotal, 3) }}</td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-end fw-bold">Tax Total:</td>
                                <td colspan="3" class="text-end">${{ number_format($taxTotal, 3) }}</td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="8" class="text-end fw-bold">Grand Total:</td>
                                <td colspan="3" class="text-end fw-bold">
                                    ${{ number_format($subTotal + $taxTotal, 3) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-4 d-flex flex-wrap gap-2">
                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-outline-warning">
                    <i class="bx bx-edit"></i> Edit
                </a>

                <a href="{{ route('vessels.invoices.index', $invoice->vessel->id) }}"
                    class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back"></i> Back
                </a>

                <a target="_blank" class="btn btn-outline-success"
                    href="{{ route('pdf.proformaInvoice.proforma_invoice', ['id' => $invoice->id, 'clickOption' => 'stream']) }}">
                    <i class="bx bx-printer"></i> Print
                </a>

                <a target="_blank" class="btn btn-success"
                    href="{{ route('pdf.proformaInvoice.proforma_invoice', ['id' => $invoice->id, 'clickOption' => 'download']) }}">
                    <i class="bx bx-download"></i> Download
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
