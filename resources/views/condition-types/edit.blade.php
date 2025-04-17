<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('fixed_fees.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
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
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">تعديل نوع الشرط</h5>
                                <a href="{{ route('condition-types.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-right"></i> العودة للقائمة
                                </a>
                            </div>

                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('condition-types.update', $conditionType) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="code" class="form-label">الكود <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('code') is-invalid @enderror" id="code"
                                                name="code" value="{{ old('code', $conditionType->code) }}" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">مثال: vessel_type، gt_size، call_type</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="name" class="form-label">الاسم <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('name') is-invalid @enderror" id="name"
                                                name="name" value="{{ old('name', $conditionType->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">مثال: نوع السفينة، الحجم الإجمالي، نوع
                                                الزيارة</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="data_type" class="form-label">نوع البيانات <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('data_type') is-invalid @enderror"
                                                id="data_type" name="data_type" required>
                                                <option value="">-- اختر نوع البيانات --</option>
                                                @foreach ($dataTypes as $value => $label)
                                                    <option value="{{ $value }}"
                                                        {{ old('data_type', $conditionType->data_type) == $value ? 'selected' : '' }}>
                                                        {{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('data_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="operator_type" class="form-label">نوع العمليات <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('operator_type') is-invalid @enderror"
                                                id="operator_type" name="operator_type" required>
                                                <option value="">-- اختر نوع العمليات --</option>
                                                @foreach ($operatorTypes as $value => $label)
                                                    <option value="{{ $value }}"
                                                        {{ old('operator_type', $conditionType->operator_type) == $value ? 'selected' : '' }}>
                                                        {{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('operator_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">العمليات المتاحة <span
                                                class="text-danger">*</span></label>
                                        <div id="operators-container" class="border rounded p-3">
                                            <!-- سيتم تحديثه ديناميكيًا -->
                                        </div>
                                        @error('available_operators')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="active" name="active"
                                            {{ old('active', $conditionType->active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active">مفعل</label>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">تحديث</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
   

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const operatorTypeSelect = document.getElementById('operator_type');
            const operatorsContainer = document.getElementById('operators-container');

            // بيانات العمليات المتاحة لكل نوع
            const availableOperators = @json($availableOperators);

            // ترجمة العمليات
            const operatorLabels = {
                '=': 'يساوي',
                '!=': 'لا يساوي',
                '>': 'أكبر من',
                '>=': 'أكبر من أو يساوي',
                '<': 'أصغر من',
                '<=': 'أصغر من أو يساوي',
                'in': 'ضمن القائمة',
                'not_in': 'ليس ضمن القائمة',
                'between': 'بين قيمتين',
                'contains': 'يحتوي على',
                'starts_with': 'يبدأ بـ',
                'ends_with': 'ينتهي بـ'
            };

            // العمليات المحددة سابقًا
            const selectedOperators = @json($selectedOperators);

            // مستمع حدث لتغيير نوع العمليات
            operatorTypeSelect.addEventListener('change', function() {
                updateAvailableOperators();
            });

            // تحديث العمليات المتاحة بناءً على نوع العمليات المحدد
            function updateAvailableOperators() {
                const operatorType = operatorTypeSelect.value;

                if (!operatorType) {
                    operatorsContainer.innerHTML = `
                    <div class="alert alert-info">
                        يرجى اختيار نوع العمليات أولاً لعرض العمليات المتاحة.
                    </div>
                `;
                    return;
                }

                const operators = availableOperators[operatorType] || [];

                if (operators.length === 0) {
                    operatorsContainer.innerHTML = `
                    <div class="alert alert-warning">
                        لا توجد عمليات متاحة لنوع العمليات المحدد.
                    </div>
                `;
                    return;
                }

                let html = '<div class="row">';

                operators.forEach(op => {
                    const label = operatorLabels[op] || op;
                    const checked = selectedOperators.includes(op) ? 'checked' : '';

                    html += `
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="available_operators[]" value="${op}" id="op_${op}" ${checked}>
                            <label class="form-check-label" for="op_${op}">
                                ${label}
                            </label>
                        </div>
                    </div>
                `;
                });

                html += '</div>';
                operatorsContainer.innerHTML = html;
            }

            // تحديث العمليات المتاحة عند تحميل الصفحة
            updateAvailableOperators();
        });
    </script>
</x-app-layout>
