<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
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
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Add invoice</h5>
                        </div>
                        <div class="card-body">
                            <form id="createInvoiceForm" action="{{ route('invoices.store') }}" method="POST">
                                @csrf
                                <input type="hidden" id="invoice_id" name="invoice_id">
                                <!-- حقل مخفي لتخزين ID السفينة التي يتم تعديلها -->

                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="invoice_type">نوع الفاتورة</label>
                                        <select name="invoice_type" id="invoice_type" class="form-control" required>
                                            <option value="proforma">بروفورما</option>
                                            <option value="final">نهائية</option>
                                        </select>
                                    </div>

                                    <div class="grid-item">
                                        <label for="vessel_id">السفينة</label>
                                        <select name="vessel_id" id="vessel_id" class="form-control" required>
                                            <option value="">اختر سفينة</option>
                                            @foreach ($vessels as $vessel)
                                                <option value="{{ $vessel->id }}">{{ $vessel->vessel_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="grid-item">
                                        <label for="invoice_date">تاريخ الفاتورة</label>
                                        <input type="date" name="invoice_date" id="invoice_date" class="form-control"
                                            required>
                                    </div>
                                </div>

                                <h4>Invoice Fees</h4>
                                <table id="feesTable" class="table">
                                    <thead>
                                        <tr>
                                            <th>اختيار</th>
                                            <th>اسم الرسم</th>
                                            <th>الوصف</th>
                                            <th>العدد</th>
                                            <th>القيمة</th>
                                            <th>نسبة الضريبة (%)</th>
                                            <th>قيمة الضريبة</th>
                                            <th>الإجمالي</th>
                                            <th>إزالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="checkbox" class="fee-checkbox"
                                                    name="fees[new][selected]" value="1"></td>
                                            <td>
                                                <select name="fees[new][fee_id]" class="form-control fee-select">
                                                    <option value="">اختر الرسم</option>
                                                    @foreach ($fixedFees as $fee)
                                                        <option value="{{ $fee->id }}">{{ $fee->fee_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" name="fees[new][description]"
                                                    class="form-control description"></td>
                                            <td><input type="number" name="fees[new][quantity]"
                                                    class="form-control quantity" min="1" value="1">
                                            </td>
                                            <td><input type="number" name="fees[new][amount]"
                                                    class="form-control amount"></td>
                                            <td><input type="number" name="fees[new][tax]"
                                                    class="form-control tax"></td>
                                            <td class="total-taxamount">0</td>
                                            <td class="total-amount">0</td>
                                            <td><button type="button" class="btn btn-danger remove-fee">X</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th id="sub-total">0</th>
                                        <th id="total-tax">0</th>
                                        <th id="total">0</th>
                                    </tfoot>
                                </table>

                                
                                <button type="button" id="addFeeRow" class="btn btn-info">إضافة رسم
                                    جديد</button>

                                <button type="submit" class="btn btn-info">حفظ الفاتورة</button>


                                <div class="d-flex justify-content-end mt-3">
                                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                                    <button type="submit" id="submit-button" class="btn btn-primary">Save</button>
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

            let fixedFeesElement = document.getElementById("fixedFeesData");
            let fixedFees = fixedFeesElement ? JSON.parse(fixedFeesElement.textContent) : [];

            $(".fee-select").select2({
                placeholder: "اختر الرسم",
                allowClear: true
            }).on('change', function() {
                let row = this.closest("tr"); // تحديد الصف الذي تم التغيير فيه
                let feeId = this.value;

                if (feeId) {
                    // البحث عن الرسوم في fixedFees باستخدام feeId
                    let fee = fixedFees.find(fee => fee.id == feeId);
                    if (fee) {
                        // تحديث الحقول
                        row.querySelector(".description").value = fee.description || '';
                        row.querySelector(".amount").value = fee.amount || 0;
                        row.querySelector(".tax").value = fee.tax || 0;
                        updateTotal(row); // تحديث المجموع
                    }
                }
            });

            function updateTotal(row) {
                let quantity = parseFloat(row.querySelector(".quantity").value) || 1;
                let amount = parseFloat(row.querySelector(".amount").value) || 0;
                let tax = parseFloat(row.querySelector(".tax").value) || 0;

                let total = (quantity * amount) + (quantity * amount * (tax / 100));
                let totalTax = (total * (tax / 100));

                row.querySelector(".total-amount").innerText = total.toFixed(3);
                row.querySelector(".total-taxamount").innerText = totalTax.toFixed(3);
                updateInvoiceTotal(); // تحديث إجمالي الفاتورة بعد كل تغيير في صف
            }

            function updateInvoiceTotal() {
                let fullSubTotal = 0;
                let fullTotalTax = 0;
                let fullTotal = 0;

                // جمع المجموع الكلي لجميع الصفوف
                document.querySelectorAll("tr").forEach(row => {
                    let amountElement = row.querySelector(".amount");
                    let taxamountElement = row.querySelector(".total-taxamount");
                    let totalAmountElement = row.querySelector(".total-amount");

                    // تأكد من أن العناصر موجودة قبل استخدامها
                    if (amountElement && taxamountElement && totalAmountElement) {
                        let subTotal = parseFloat(amountElement.value) || 0;
                        let totalTax = parseFloat(taxamountElement.innerText) || 0;
                        let total = parseFloat(totalAmountElement.innerText) || 0;

                        fullSubTotal += subTotal;
                        fullTotalTax += totalTax;
                        fullTotal += total;
                    }
                });

                // عرض إجمالي الفاتورة
                document.getElementById("sub-total").innerText = fullSubTotal.toFixed(3);
                document.getElementById("total-tax").innerText = fullTotalTax.toFixed(3);
                document.getElementById("total").innerText = fullTotal.toFixed(3);
            }




            document.addEventListener("input", function(event) {
                if (event.target.matches(".quantity, .amount, .tax")) {
                    updateTotal(event.target.closest("tr"));
                }
            });

            document.getElementById("addFeeRow").addEventListener("click", function() {
                let table = document.getElementById("feesTable").getElementsByTagName("tbody")[0];
                let row = document.createElement("tr");

                let options = '<option value="">اختر الرسم</option>';
                fixedFees.forEach(fee => {
                    options += `<option value="${fee.id}">${fee.fee_name}</option>`;
                });

                row.innerHTML = `
                    <td><input type="checkbox" class="fee-checkbox" name="fees[new][selected]" value="1"></td>
                    <td>
                        <select name="fees[new][fee_id]" class="form-control fee-select">${options}</select>
                    </td>
                    <td><input type="text" name="fees[new][description]" class="form-control description"></td>
                    <td><input type="number" name="fees[new][quantity]" class="form-control quantity" min="1" value="1"></td>
                    <td><input type="number" name="fees[new][amount]" class="form-control amount"></td>
                    <td><input type="number" name="fees[new][tax]" class="form-control tax" value="0"></td>
                    <td class="total-taxamount">0</td>
                    <td class="total-amount">0</td>
                    <td><button type="button" class="btn btn-danger remove-fee">X</button></td>
                `;
                table.appendChild(row);

                // **تفعيل Select2 بعد إضافة الصف الجديد**
                $(row).find(".fee-select").select2({
                    placeholder: "اختر الرسم",
                    allowClear: true
                }).on('change', function() {
                    let feeId = this.value;
                    if (feeId) {
                        // البحث عن الرسوم في fixedFees باستخدام feeId
                        let fee = fixedFees.find(fee => fee.id == feeId);
                        if (fee) {
                            // تحديث الحقول
                            row.querySelector(".description").value = fee.description || '';
                            row.querySelector(".amount").value = fee.amount || 0;
                            row.querySelector(".tax").value = fee.tax || 0;
                            updateTotal(row); // تحديث المجموع
                        }
                    }
                });

                row.querySelector(".remove-fee").addEventListener("click", function() {
                    row.remove();
                });
            });

            document.addEventListener("click", function(event) {
                if (event.target.matches(".remove-fee")) {
                    event.target.closest("tr").remove();
                }
            });
        });
    </script>
    <!-- تحميل مكتبة Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</x-app-layout>
