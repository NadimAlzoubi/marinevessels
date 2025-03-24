<x-app-layout>
    <div class="container">
        <h1>تحرير الفاتورة</h1>
        <form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="invoice_type">نوع الفاتورة</label>
                <select name="invoice_type" id="invoice_type" class="form-control" required>
                    <option value="proforma" {{ $invoice->invoice_type == 'proforma' ? 'selected' : '' }}>بروفورما
                    </option>
                    <option value="final" {{ $invoice->invoice_type == 'final' ? 'selected' : '' }}>نهائية</option>
                </select>
            </div>
            <div class="form-group">
                <label for="vessel_id">السفينة</label>
                <select name="vessel_id" id="vessel_id" class="form-control" required>
                    @foreach ($vessels as $vessel)
                        <option value="{{ $vessel->id }}" {{ $invoice->vessel_id == $vessel->id ? 'selected' : '' }}>
                            {{ $vessel->vessel_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="invoice_date">تاريخ الفاتورة</label>
                <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                    value="{{ $invoice->invoice_date }}" required>
            </div>

            <h4>Invoice Fees</h4>
            <div>
                @foreach ($fixedFees as $fee)
                    <label>
                        <input type="checkbox" name="fees[]" value="{{ $fee->id }}"
                            {{ in_array($fee->id, $invoiceFees) ? 'checked' : '' }}>
                        {{ $fee->name }} - ${{ $fee->amount }} - Tax: {{ $fee->tax_rate }}%
                    </label>
                    <br>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-3">تحديث الفاتورة</button>
        </form>
    </div>
</x-app-layout>
