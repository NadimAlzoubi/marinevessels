<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('vessels.invoices.index', $vessel->id) }}" class="btn btn-secondary mb-3 mt-3">Back</a>
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
            <div class="row" id="invoice-form">
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">تعديل فاتورة</h5>
                        </div>
                        <div class="card-body">
                            <form id="editInvoiceForm"
                                action="{{ route('vessels.invoices.update', [$vessel->id, $invoice->id]) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="vessel_id" value="{{ $vessel->id }}">
                                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label>Vessel</label>
                                        <input type="text" class="form-control"
                                            value="{{ ucfirst($vessel->vessel_name) }}" disabled>
                                    </div>

                                    <div class="grid-item">
                                        <label>Service Type</label>
                                        @if ($invoice->invoice_type == 'draft')
                                            <select name="invoice_type" class="form-control">
                                                <option value="draft" selected>Draft</option>
                                                <option value="proforma">Proforma</option>
                                                <option value="preliminary">Preliminary</option>
                                                <option value="final">Final</option>
                                            </select>
                                        @else
                                            <input type="text" class="form-control"
                                                value="{{ ucfirst($invoice->invoice_type) }}" disabled>
                                        @endif

                                    </div>


                                    <div class="grid-item">
                                        <label for="call_type">Call Type</label>
                                        <select name="call_type" id="call_type" class="form-control" required>
                                            <option value="lay-by"
                                                {{ $invoice->call_type == 'lay-by' ? 'selected' : '' }}>Lay By</option>
                                            <option value="load"
                                                {{ $invoice->call_type == 'load' ? 'selected' : '' }}>Load</option>
                                            <option value="discharge"
                                                {{ $invoice->call_type == 'discharge' ? 'selected' : '' }}>Discharge
                                            </option>
                                        </select>
                                    </div>
                                    <div class="grid-item">
                                        <label for="currency">Currency</label>
                                        <select name="currency" id="currency" class="form-control">
                                            @foreach (['USD', 'EUR', 'AED', 'SAR'] as $curr)
                                                <option value="{{ $curr }}"
                                                    {{ $invoice->currency == $curr ? 'selected' : '' }}>
                                                    {{ $curr }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid-item">
                                        <label for="invoice_date">Service Date</label>
                                        <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                            value="{{ $invoice->invoice_date }}" required>
                                    </div>
                                </div>

                                <h4 class="mt-4">Fees</h4>
                                <table id="feesTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Fee Name</th>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Discount Rate (%)</th>
                                            <th>Discount Amount</th>
                                            <th>Sub Amount</th>
                                            <th>Tax Rate (%)</th>
                                            <th>Tax Amount</th>
                                            <th>Total</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- {{ dd($invoice) }} --}}
                                        {{-- {{dd($invoice->fees)}} --}}
                                        @foreach ($invoice->fees as $index => $fee)
                                            <tr>
                                                <td>
                                                    <select name="fees[{{ $index }}][fixed_fee_id]"
                                                        class="form-control fee-select"
                                                        data-selected-id="{{ $fee->pivot->fixed_fee_id ?? '' }}">
                                                </td>
                                                <td><input type="text" name="fees[{{ $index }}][description]"
                                                        class="form-control description"
                                                        value="{{ $fee->description }}"></td>
                                                <td><input type="number" name="fees[{{ $index }}][quantity]"
                                                        class="form-control quantity" min="1"
                                                        value="{{ $fee->pivot->quantity }}"></td>
                                                <td><input type="number" name="fees[{{ $index }}][amount]"
                                                        class="form-control amount" value="{{ $fee->pivot->amount }}">
                                                </td>
                                                <td><input type="number" name="fees[{{ $index }}][discount]"
                                                        class="form-control discount"
                                                        value="{{ $fee->pivot->discount }}"></td>
                                                <td class="total-discountamount">0</td>
                                                <td class="total-subamount">0</td>
                                                <td><input type="number" name="fees[{{ $index }}][tax_rate]"
                                                        class="form-control tax" value="{{ $fee->pivot->tax_rate }}">
                                                </td>
                                                <td class="total-taxamount">0</td>
                                                <td class="total-amount">0</td>
                                                <td><button type="button" class="btn btn-danger remove-fee"><i
                                                            class='bx bx-x'></i></button></td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2"></td>
                                            <th>Subtotal</th>
                                            <td id="sub-total">0</td>
                                            <td></td>
                                            <td id="total-discount">0</td>
                                            <td id="total-subamount">0</td>
                                            <td></td>
                                            <td id="total-tax">0</td>
                                            <td id="total">0</td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <button type="button" id="addFeeRow" class="btn btn-info">Add New Fee</button>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script id="fixedFeesData" type="application/json">
        @json($fixedFees)
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const fixedFees = JSON.parse(document.getElementById("fixedFeesData").textContent || "[]");
            let feeIndex = document.querySelectorAll("#feesTable tbody tr").length;

            // تحديث كل الصفوف الحالية
            document.querySelectorAll("#feesTable tbody tr").forEach(row => {
                setupRow(row);
            });

            // إضافة سطر جديد عند الضغط على زر الإضافة
            document.getElementById("addFeeRow").addEventListener("click", function() {
                const tableBody = document.querySelector("#feesTable tbody");
                const row = renderFeeRow();
                tableBody.appendChild(row);
            });

            // إعداد التاريخ الافتراضي
            const currentDate = new Date();
            const formattedDate = currentDate.toISOString().split('T')[0];
            document.getElementById('invoice_date').value = formattedDate;

            // إعداد كل الحقول في صف معين
            function setupRow(row) {
                row.querySelectorAll(".quantity, .amount, .tax, .discount").forEach(input => {
                    input.addEventListener("input", () => updateTotal(row));
                });

                const removeBtn = row.querySelector(".remove-fee");
                if (removeBtn) {
                    removeBtn.addEventListener("click", () => {
                        row.remove();
                        updateInvoiceTotal();
                    });
                }

                const $select = $(row).find(".fee-select");
                const selectedId = $select.data('selected-id');
                console.log('selected ID:', selectedId);

                $select.empty().append('<option value="">--Choose--</option>');

                fixedFees.forEach(fee => {
                    const isSelected = (fee.id == selectedId) ? 'selected' : '';
                    $select.append(`<option value="${fee.id}" ${isSelected}>${fee.fee_name}</option>`);
                });


                $select.select2({
                    placeholder: "--Choose Fee--",
                    allowClear: true,
                    width: '100%',
                    dropdownAutoWidth: true
                }).on('change', function() {
                    const feeId = this.value;
                    const fee = fixedFees.find(f => f.id == feeId);
                    if (fee) {
                        row.querySelector(".description").value = fee.description || '';
                        row.querySelector(".amount").value = fee.amount || 0;
                        updateTotal(row);
                    }
                });

                updateTotal(row);
            }

            // إنشاء صف جديد
            function renderFeeRow() {
                const row = document.createElement("tr");
                const index = feeIndex++;

                let options = `<option value="">--Choose--</option>`;
                options += fixedFees.map(fee =>
                    `<option value="${fee.id}">${fee.fee_name}</option>`
                ).join('');

                row.innerHTML = `
                    <td><select name="fees[${index}][fixed_fee_id]" class="form-control fee-select"></select></td>
                    <td><input type="text" name="fees[${index}][description]" class="form-control description"></td>
                    <td><input type="number" name="fees[${index}][quantity]" class="form-control quantity" min="1" value="1"></td>
                    <td><input type="number" name="fees[${index}][amount]" class="form-control amount"></td>
                    <td><input type="number" name="fees[${index}][discount]" class="form-control discount" value="0"></td>
                    <td class="total-discountamount">0</td>
                    <td class="total-subamount">0</td>
                    <td><input type="number" name="fees[${index}][tax_rate]" class="form-control tax" value="0"></td>
                    <td class="total-taxamount">0</td>
                    <td class="total-amount">0</td>
                    <td><button type="button" class="btn btn-danger remove-fee"><i class='bx bx-x'></i></button></td>
                `;

                setupRow(row);
                return row;
            }

            // دوال الحسابات
            function getNumber(value) {
                return parseFloat(value) || 0;
            }

            function updateTotal(row) {
                const quantity = getNumber(row.querySelector(".quantity").value);
                const amount = getNumber(row.querySelector(".amount").value);
                const tax = getNumber(row.querySelector(".tax").value);
                const discount = getNumber(row.querySelector(".discount").value);

                const subtotal = quantity * amount;
                const discounted = (discount / 100) * subtotal;
                const discountedTotal = subtotal - discounted;
                const taxAmount = discountedTotal * (tax / 100);
                const total = discountedTotal + taxAmount;
                const subamount = subtotal - discounted;

                row.querySelector(".total-taxamount").innerText = taxAmount.toFixed(3);
                row.querySelector(".total-subamount").innerText = subamount.toFixed(3);
                row.querySelector(".total-discountamount").innerText = discounted.toFixed(3);
                row.querySelector(".total-amount").innerText = total.toFixed(3);

                updateInvoiceTotal();
            }

            function updateInvoiceTotal() {
                let fullSubTotal = 0;
                let fullTaxTotal = 0;
                let fullDiscountTotal = 0;
                let fullSubAmount = 0;
                let fullTotal = 0;

                document.querySelectorAll("#feesTable tbody tr").forEach(row => {
                    const quantity = getNumber(row.querySelector(".quantity").value);
                    const amount = getNumber(row.querySelector(".amount").value);
                    const taxAmount = getNumber(row.querySelector(".total-taxamount").innerText);
                    const discount = getNumber(row.querySelector(".discount").value);
                    const totalAmount = getNumber(row.querySelector(".total-amount").innerText);
                    const subtotal = quantity * amount;
                    const discountAmount = (discount / 100) * subtotal;

                    fullSubTotal += subtotal;
                    fullTaxTotal += taxAmount;
                    fullDiscountTotal += discountAmount;
                    fullSubAmount += subtotal - discountAmount;
                    fullTotal += totalAmount;
                });

                document.getElementById("sub-total").innerText = fullSubTotal.toFixed(3);
                document.getElementById("total-tax").innerText = fullTaxTotal.toFixed(3);
                document.getElementById("total-discount").innerText = fullDiscountTotal.toFixed(3);
                document.getElementById("total-subamount").innerText = fullSubAmount.toFixed(3);
                document.getElementById("total").innerText = fullTotal.toFixed(3);
            }
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</x-app-layout>
