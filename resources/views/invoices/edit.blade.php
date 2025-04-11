<x-app-layout>
    <div class="container">
        <h1>Edit Invoice</h1>

        <form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label for="invoice_type">Invoice Type</label>
                <select name="invoice_type" class="form-control" required>
                    <option value="proforma" {{ $invoice->invoice_type == 'proforma' ? 'selected' : '' }}>Proforma</option>
                    <option value="final" {{ $invoice->invoice_type == 'final' ? 'selected' : '' }}>Final</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="vessel_id">Vessel</label>
                <select name="vessel_id" class="form-control" required>
                    @foreach ($vessels as $vessel)
                        <option value="{{ $vessel->id }}" {{ $invoice->vessel_id == $vessel->id ? 'selected' : '' }}>
                            {{ $vessel->vessel_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="invoice_date">Invoice Date</label>
                <input type="date" name="invoice_date" class="form-control" value="{{ $invoice->invoice_date }}" required>
            </div>

            <h4>Invoice Fees</h4>
            <div id="fees-wrapper">
                @foreach ($invoice->fees as $fee)
                    <div class="row mb-2 fee-row">
                        {{-- <input type="hidden" name="fixed_fees[{{ $fee->id }}][fee_id]" value="{{ $fee->id }}"> --}}
                        
                        <div class="col-md-3">
                            <input type="text" class="form-control" value="{{ $fee->fee_name }}" disabled>
                        </div>

                        <div class="col-md-2">
                            <input type="number" class="form-control" value="{{ $fee->amount }}" disabled>
                        </div>

                        <div class="col-md-2">
                            <input type="number" class="form-control" value="{{ $fee->tax_rate }}" disabled>
                        </div>

                        <div class="col-md-2">
                            <input type="number" name="fixed_fees[{{ $fee->id }}][quantity]" class="form-control" value="{{ $fee->pivot->quantity }}" placeholder="Quantity">
                        </div>

                        <div class="col-md-2">
                            <input type="number" name="fixed_fees[{{ $fee->id }}][discount]" class="form-control" value="{{ $fee->pivot->discount }}" placeholder="Discount">
                        </div>

                        <div class="col-md-1 d-flex align-items-center">
                            <button type="button" class="btn btn-danger btn-sm remove-fee">X</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-4">Update Invoice</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.remove-fee').forEach(btn => {
                btn.addEventListener('click', function () {
                    this.closest('.fee-row').remove();
                });
            });
        });
    </script>
</x-app-layout>
