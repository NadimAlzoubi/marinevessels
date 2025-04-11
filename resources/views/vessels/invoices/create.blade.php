<x-app-layout>
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

        <!-- Responsive Form -->
        <div class="container-fluid mt-2">
            <div class="row" id="invoice-form">
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">إضافة فاتورة</h5>
                        </div>
                        <div class="card-body">
                            <form id="createInvoiceForm" action="{{ route('vessels.invoices.store', $vessel->id) }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="vessel_id" value="{{ $vessel->id }}">

                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label>Vessel</label>
                                        <input type="text" class="form-control" value="{{ $vessel->vessel_name }}"
                                            disabled>
                                    </div>
                                    <div class="grid-item">
                                        <label for="invoice_type">Invoice Type</label>
                                        <select name="invoice_type" id="invoice_type" class="form-control" required>
                                            <option value="proforma">Proforma</option>
                                            <option value="final">Final</option>
                                        </select>
                                    </div>
                                    <div class="grid-item">
                                        <label for="call_type">Call Type</label>
                                        <select name="call_type" id="call_type" class="form-control" required>
                                            <option value="lay-by">Lay By</option>
                                            <option value="load">Load</option>
                                            <option value="discharge">Discharge</option>
                                        </select>
                                    </div>
                                    <div class="grid-item">
                                        <label for="currency">Currency</label>
                                        <select name="currency" id="currency" class="form-control">
                                            <option value="USD" selected>USD - US Dollar</option>
                                            <option value="EUR">EUR - Euro</option>
                                            <option value="AED">AED - UAE Dirham</option>
                                            <option value="SAR">SAR - Saudi Riyal</option>
                                        </select>
                                    </div>

                                    <div class="grid-item">
                                        <label for="invoice_date">Invoice Date</label>
                                        <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                            required>
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
                                        <!-- Initial Row -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2"></td>
                                            <th>Subtotal</th>
                                            <td id="sub-total">0</td>
                                            <td id=""></td>
                                            <td id="total-discount">0</td>
                                            <td id="total-subamount">0</td>
                                            <td id=""></td>
                                            <td id="total-tax">0</td>
                                            <td id="total">0</td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <button type="button" id="addFeeRow" class="btn btn-info">Add New Fee</button>

                                <div class="d-flex justify-content-end mt-3">
                                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
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
            function getNumber(value) {
                return parseFloat(value) || 0;
            }
            function updateTotal(row) {
                const quantity = getNumber(row.querySelector(".quantity").value);
                const amount = getNumber(row.querySelector(".amount").value);
                const tax = getNumber(row.querySelector(".tax").value);
                const discount = getNumber(row.querySelector(".discount").value);
                const subtotal = quantity * amount;
                const discountedTotal = subtotal - (discount / 100) * subtotal;
                const discounted = (discount / 100) * subtotal;
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
                let fullTotal = 0;
                document.querySelectorAll("#feesTable tbody tr").forEach(row => {
                    const quantity = getNumber(row.querySelector(".quantity").value);
                    const amount = getNumber(row.querySelector(".amount").value);
                    const taxAmount = getNumber(row.querySelector(".total-taxamount").innerText);
                    const discount = getNumber(row.querySelector(".discount").value);
                    const totalAmount = getNumber(row.querySelector(".total-amount").innerText);
                    fullSubTotal += quantity * amount;
                    fullTaxTotal += taxAmount;
                    fullDiscountTotal += (discount / 100) * (quantity * amount); // احتساب الخصم
                    fullTotal += totalAmount;
                    fullSubAmount = fullSubTotal - fullDiscountTotal;
                });
                document.getElementById("sub-total").innerText = fullSubTotal.toFixed(3);
                document.getElementById("total-tax").innerText = fullTaxTotal.toFixed(3);
                document.getElementById("total-subamount").innerText = fullSubAmount.toFixed(3);
                document.getElementById("total-discount").innerText = fullDiscountTotal.toFixed(3);
                document.getElementById("total").innerText = fullTotal.toFixed(3);
            }
            function renderFeeRow() {
                const row = document.createElement("tr");
                let options = `<option value="" selected>--Choose--</option>`;
                options += fixedFees.map(fee =>
                    `<option value="${fee.id}">${fee.fee_name}</option>`
                ).join('');

                row.innerHTML = `
            <td>
                <select name="fees[new][fixed_fee_id]" class="form-control fee-select">${options}</select>
            </td>
            <td><input type="text" name="fees[new][description]" class="form-control description"></td>
            <td><input type="number" name="fees[new][quantity]" class="form-control quantity" min="1" value="1"></td>
            <td><input type="number" name="fees[new][amount]" class="form-control amount"></td>
            <td><input type="number" name="fees[new][discount]" class="form-control discount" value="0"></td>
            <td class="total-discountamount">0</td>
            <td class="total-subamount">0</td>
            <td><input type="number" name="fees[new][tax_rate]" class="form-control tax" value="0"></td>
            <td class="total-taxamount">0</td>
            <td class="total-amount">0</td>
            <td><button type="button" class="btn btn-danger remove-fee"><i class='bx bx-x'></i></button></td>
        `;

                $(row).find(".fee-select").select2({
                    placeholder: "--Choose Fee--",
                    allowClear: true
                }).on('change', function() {
                    const feeId = this.value;
                    const fee = fixedFees.find(f => f.id == feeId);
                    if (fee) {
                        row.querySelector(".description").value = fee.description || '';
                        row.querySelector(".amount").value = fee.amount || 0;
                        updateTotal(row);
                    }
                });

                row.querySelector(".remove-fee").addEventListener("click", () => row.remove());
                row.querySelectorAll(".quantity, .amount, .tax, .discount").forEach(input => {
                    input.addEventListener("input", () => updateTotal(row));
                });

                return row;
            }

            document.getElementById("addFeeRow").addEventListener("click", function() {
                const tableBody = document.querySelector("#feesTable tbody");
                tableBody.appendChild(renderFeeRow());
            });

            // Adding initial row
            document.getElementById("addFeeRow").click();
        });
    </script>

    <!-- Select2 Library -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</x-app-layout>
