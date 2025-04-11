<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-4">Invoice Details</h2>

        <div class="card mb-4">
            <div class="card-body">
                <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Invoice Type:</strong> {{ ucfirst($invoice->invoice_type) }}</p>
                <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date }}</p>
                <p><strong>Vessel:</strong> {{ $invoice->vessel->vessel_name ?? '-' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Fees</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered m-0">
                    <thead class="table-light">
                        <tr>
                            <th>Fee Name</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Discount</th>
                            <th>Tax Rate (%)</th>
                            <th>Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $subTotal = 0;
                            $taxTotal = 0;

                        @endphp

                        @forelse (optional($invoice->fees)->all() as $fee)
                            @php
                                $quantity = $fee->pivot->quantity;
                                $discount = $fee->pivot->discount;
                                $price = $fee->amount;
                                $taxRate = $fee->tax_rate;

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
                                <td>{{ $fee->description }}</td>
                                <td>{{ $quantity }}</td>
                                <td>${{ number_format($price, 2) }}</td>
                                <td>{{ $discount }}%</td>
                                <td>{{ $taxRate }}%</td>
                                <td>${{ number_format($lineTotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No fees added to this invoice.</td>
                            </tr>
                        @endforelse



                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="6" class="text-end">Sub Total:</th>
                            <th>${{ number_format($subTotal, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-end">Tax Total:</th>
                            <th>${{ number_format($taxTotal, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-end">Grand Total:</th>
                            <th>${{ number_format($subTotal + $taxTotal, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('vessels.invoices.index', $invoice->vessel->id) }}" class="btn btn-secondary">Back</a>

            <a target="_blank" class="btn btn-success" href=" {{ route('pdf.proformaInvoice.proforma_invoice', ['id' => $invoice->id, 'clickOption' => 'stream']) }}">
                <i class="bx bx-printer"></i> Print 
            </a>


            <a target="_blank" class="btn btn-success" href=" {{ route('pdf.proformaInvoice.proforma_invoice', ['id' => $invoice->id, 'clickOption' => 'download']) }}">
                <i class="bx bx-printer"></i> Download
            </a>

        </div>
    </div>
</x-app-layout>
