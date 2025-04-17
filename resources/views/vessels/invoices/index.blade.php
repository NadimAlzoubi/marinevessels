<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <div class="text text-center">{{ $vessel->job_no }} | {{ ucfirst($vessel->vessel_name) }}</div>
            <a href="{{ route('vessels.index') }}" class="btn btn-secondary mb-3">Back</a>
            <a href="{{ route('vessels.invoices.create', ['vessel' => $vessel->id]) }}"
                class="btn btn-success mb-3">Create New Service
                +</a>
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

            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Vessel invoices</h5>
                        </div>
                        <div class="card-body">
                            <table id="vessel-invoices-table" class="display">
                                <thead>
                                    <tr>
                                        <th>Inv No.</th>
                                        <th>Service Type</th>
                                        <th>Date</th>
                                        {{-- <th>Vessel</th> --}}
                                        <th>Subtotal</th>
                                        <th>Tax Total</th>
                                        <th>Grand Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            $('#vessel-invoices-table').DataTable({
                dom: '<"row"<"col-md-6 custom-length"l><"col-md-6 custom-search"f>>' +
                    '<"row"<"col-md-12"B>>' +
                    '<"row"<"col-md-12"rt>>' +
                    '<"row"<"col-md-6 custom-info"i><"col-md-6 custom-pagination"p>>',
                language: {
                    lengthMenu: "_MENU_",
                    search: "<i class='bx bx-search-alt-2'></i> Search: ",
                    info: "Showing _START_ to _END_ of _TOTAL_ records",
                    infoEmpty: "No records available",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                buttons: [{
                        extend: 'copy',
                        text: '<i class="bx bx-copy"></i> Copy',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bx bx-export"></i> Export to Excel',
                        title: null,
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    },


                    {
                        extend: 'pdf',
                        text: '<i class="bx bx-file"></i> Export to PDF',
                        orientation: 'landscape', // جعل الصفحة أفقية
                        pageSize: 'A4', // تحديد حجم الورق
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }, // استبعاد عمود "الإجراءات"
                        customize: function(doc) {
                            doc.styles.tableHeader = {
                                bold: true,
                                fontSize: 12,
                                color: 'white',
                                fillColor: '#343a40'
                            };
                            doc.content[0].text = 'Vessels';
                            doc.styles.title = {
                                fontSize: 16,
                                bold: true,
                                alignment: 'center'
                            };
                            doc.pageMargins = [20, 20, 20, 20]; // تحديد الهوامش

                            // ضبط عرض الجدول ليأخذ 100% من عرض الصفحة
                            let table = doc.content[1].table;
                            table.widths = Array(table.body[0].length).fill(
                                '*'); // يجعل كل الأعمدة بنفس العرض
                            table.dontBreakRows = true; // منع تقسيم الجدول بين الصفحات
                            table.layout = 'lightHorizontalLines'; // تعيين نمط الجدول
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="bx bx-printer" ></i> Print',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
                columnDefs: [{
                        orderable: false,
                        searchable: false,
                        targets: -1
                    } // Make Action column non-searchable and non-orderable
                ],
                order: [
                    [0, 'desc']
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route('vessels.invoices.index', ['vessel' => $vessel->id]) }}',
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ], // إعادة تفعيل خيارات تحديد العدد
                columns: [{
                        data: 'invoice_number'
                    },
                    {
                        data: 'invoice_type',
                        render: function(data, type, row) {
                            if (typeof data !== 'string') return data;
                            return data.charAt(0).toUpperCase() + data.slice(1);
                        }
                    },
                    {
                        data: 'invoice_date'
                    },
                    // {
                    //     data: 'vessel_info'
                    // },
                    {
                        data: 'sub_total'
                    },
                    {
                        data: 'tax_total'
                    },
                    {
                        data: 'grand_total'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ]
            });

        });
    </script>
    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.colVis.min.js"></script>
    <!-- JSZip & PDFMake لدعم تصدير Excel و PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
</x-app-layout>
