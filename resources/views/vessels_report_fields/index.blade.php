<x-app-layout>
    <section class="home-section">
        <div class="text">Vessels</div>
        @if (session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li style="color: red;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Responsive Form -->
        <div class="container-fluid mt-2">
            <div class="row" id="vessel-form">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Shipping Form</h5>
                        </div>
                        <div class="card-body">

                            <form id="add-vessel-report-fields">
                                @csrf
                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="label" class="form-label">Field Label</label>
                                        <input type="text" class="form-control" id="label" name="label"
                                            placeholder="Enter field label">
                                    </div>
                                    <div class="grid-item">
                                        <label for="name" class="form-label">Field Name (Use _)</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter field name">
                                    </div>
                                    <div class="grid-item">
                                        <label for="type" class="form-label">Field Type</label>
                                        <select id="type" name="type" class="form-select">
                                            <option value="text">Text</option>
                                            <option value="number">Number</option>
                                            <option value="date">Date</option>
                                        </select>
                                    </div>
                                    <div class="grid-item">
                                        <label for="placeholder" class="form-label">Placeholder</label>
                                        <input type="text" class="form-control" id="placeholder" name="placeholder"
                                            placeholder="Enter placeholder">
                                    </div>
                                    <div class="grid-item">
                                        <label for="category" class="form-label">Category</label>
                                        <select id="category" name="category" class="form-select">
                                            <option value="Basic Data">Basic Data</option>
                                            <option value="Timeline">Timeline</option>
                                            <option value="Bunkers ROB">Bunkers ROB</option>
                                            <option value="Extra">Extra</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                                    <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive datatable -->
        <div class="container-fluid mt-2">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Vessels</h5>
                        </div>
                        <div class="card-body">
                            <table id="vessels-report-fields-table" class="display">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Field Label</th>
                                        <th>Field Name</th>
                                        <th>Field Type</th>
                                        <th>Placeholder</th>
                                        <th>Category</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- عمليات السفن --}}
    <script>
        // {{-- عرض السفن --}}
        $(document).ready(function() {
            $('#vessels-report-fields-table').DataTable({
                order: [
                    [0, 'desc']
                ],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route('vessels_report_fields.index') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'label'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'type'
                    },
                    {
                        data: 'placeholder'
                    },
                    {
                        data: 'category'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ]
            });


            // {{-- تخزين الحقول --}}  
            $('#add-vessel-report-fields').on('submit', function(e) {
                e.preventDefault(); // منع الإرسال الافتراضي للنموذج
                var url = '{{ route('vessels_report_fields.store') }}'; // رابط عملية الحفظ

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $(this).serialize(), // إرسال البيانات من النموذج
                    success: function(response) {
                        alert(response.message); // عرض رسالة نجاح
                        $('#add-vessel-report-fields')[0].reset(); // تفريغ النموذج
                        $('#vessels-report-fields-table').DataTable().ajax
                            .reload(); // إعادة تحميل الجدول
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessages = '';
                        for (var key in errors) {
                            errorMessages += errors[key][0] + '\n'; // عرض الأخطاء
                        }
                        alert(errorMessages);
                    }
                });
            });


        });
    </script>

</x-app-layout>
