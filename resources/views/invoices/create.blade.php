{{-- <x-app-layout>
    <div class="container py-4">
        <h2>Create Invoice</h2>
        <form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
            @csrf

            <div class="mb-3">
                <label for="invoice_type" class="form-label">Invoice Type</label>
                <select name="invoice_type" class="form-control" required>
                    <option value="proforma">Proforma</option>
                    <option value="final">Final</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="vessel_id" class="form-label">Vessel</label>
                <select name="vessel_id" class="form-control" required>
                    @foreach ($vessels as $vessel)
                        <option value="{{ $vessel->id }}">{{ $vessel->vessel_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="invoice_date" class="form-label">Invoice Date</label>
                <input type="date" name="invoice_date" class="form-control" required>
            </div>

            <hr>
            <h4>Invoice Fees</h4>
            <div id="fee-items"></div>

            <button type="button" class="btn btn-secondary my-2" onclick="addFeeItem()">Add Fee</button>

            <hr>
            <button type="submit" class="btn btn-primary">Save Invoice</button>
        </form>
    </div>

    <template id="fee-template">
        <div class="card mb-3 p-3 fee-item">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Fee</label>
                    <select name="fees[][fee_id]" class="form-control fee-select" required onchange="updateFeeData(this)">
                        <option value="">Select Fee</option>
                        @foreach ($fixedFees as $fee)
                            <option value="{{ $fee->id }}"
                                data-amount="{{ $fee->amount }}"
                                data-tax="{{ $fee->tax_rate }}">
                                {{ $fee->fee_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="fees[][quantity]" class="form-control quantity" value="1" min="1" oninput="calculateTotal(this)">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Discount</label>
                    <input type="number" name="fees[][discount]" class="form-control discount" value="0" min="0" oninput="calculateTotal(this)">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total</label>
                    <input type="text" class="form-control total" readonly>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger" onclick="removeFeeItem(this)">Remove</button>
                </div>
            </div>
        </div>
    </template>

    @push('scripts')
        <script>
            function addFeeItem() {
                const template = document.getElementById('fee-template').content.cloneNode(true);
                document.getElementById('fee-items').appendChild(template);
            }

            function removeFeeItem(btn) {
                btn.closest('.fee-item').remove();
            }

            function updateFeeData(select) {
                const option = select.selectedOptions[0];
                const wrapper = select.closest('.fee-item');
                const amount = parseFloat(option.getAttribute('data-amount')) || 0;
                const tax = parseFloat(option.getAttribute('data-tax')) || 0;
                wrapper.dataset.amount = amount;
                wrapper.dataset.tax = tax;
                calculateTotal(select);
            }

            function calculateTotal(el) {
                const wrapper = el.closest('.fee-item');
                const quantity = parseFloat(wrapper.querySelector('.quantity').value) || 1;
                const discount = parseFloat(wrapper.querySelector('.discount').value) || 0;
                const amount = parseFloat(wrapper.dataset.amount) || 0;
                const tax = parseFloat(wrapper.dataset.tax) || 0;

                let subtotal = quantity * amount;
                let taxAmount = subtotal * (tax / 100);
                let total = subtotal + taxAmount - discount;
                wrapper.querySelector('.total').value = total.toFixed(2);
            }
        </script>
    @endpush
</x-app-layout> --}}
