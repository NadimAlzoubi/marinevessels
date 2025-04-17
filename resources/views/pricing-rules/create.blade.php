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

            <style>
                .condition-group {
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    padding: 15px;
                    margin-bottom: 15px;
                    background-color: #f8f9fa;
                }

                .condition-item {
                    border: 1px solid #e0e0e0;
                    border-radius: 5px;
                    padding: 10px;
                    margin-bottom: 10px;
                    background-color: #fff;
                }

                .nested-group {
                    margin-left: 20px;
                    border-left: 3px solid #007bff;
                }
            </style>

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">إضافة قاعدة تسعير جديدة</h5>
                                <a href="{{ route('pricing-rules.index') }}" class="btn btn-secondary btn-sm">
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

                                @php 
                                    // dd($tariffCategories); 
                                @endphp

                                <form action="{{ route('pricing-rules.store') }}" method="POST" id="pricing-rule-form">
                                    @csrf

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="tariff_category_id" class="form-label">فئة التعريفة <span
                                                    class="text-danger">*</span></label>
                                            <select
                                                class="form-select @error('tariff_category_id') is-invalid @enderror"
                                                id="tariff_category_id" name="tariff_category_id" required>
                                                <option value="">-- اختر فئة التعريفة --</option>
                                                @foreach ($tariffCategories as $id => $name)
                                                    <option value="{{ $id }}"
                                                        {{ old('tariff_category_id', $selectedTariffCategoryId) == $id ? 'selected' : '' }}>
                                                        {{ $name }}</option>
                                                @endforeach
                                            </select>
                                            @error('tariff_category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="name" class="form-label">اسم القاعدة <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('name') is-invalid @enderror" id="name"
                                                name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="rate" class="form-label">السعر الأساسي <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0"
                                                    class="form-control @error('rate') is-invalid @enderror"
                                                    id="rate" name="rate" value="{{ old('rate') }}" required>
                                                <span class="input-group-text">درهم</span>
                                            </div>
                                            @error('rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="priority" class="form-label">الأولوية <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="1" min="0"
                                                class="form-control @error('priority') is-invalid @enderror"
                                                id="priority" name="priority" value="{{ old('priority', 10) }}"
                                                required>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">القيمة الأعلى تعني أولوية أعلى</small>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3 form-check mt-4">
                                                <input type="checkbox" class="form-check-input" id="active"
                                                    name="active" {{ old('active') ? 'checked' : 'checked' }}>
                                                <label class="form-check-label" for="active">مفعل</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="effective_from" class="form-label">تاريخ بدء السريان</label>
                                            <input type="date"
                                                class="form-control @error('effective_from') is-invalid @enderror"
                                                id="effective_from" name="effective_from"
                                                value="{{ old('effective_from') }}">
                                            @error('effective_from')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="effective_to" class="form-label">تاريخ انتهاء السريان</label>
                                            <input type="date"
                                                class="form-control @error('effective_to') is-invalid @enderror"
                                                id="effective_to" name="effective_to"
                                                value="{{ old('effective_to') }}">
                                            @error('effective_to')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <hr>

                                    <h5 class="mb-3">شروط تطبيق القاعدة</h5>

                                    <div id="conditions-builder" class="mb-4">
                                        <div class="condition-group" id="root-group">
                                            <div class="mb-3">
                                                <label class="form-label">نوع العملية</label>
                                                <select class="form-select operator-select">
                                                    <option value="AND">AND - يجب تحقق جميع الشروط</option>
                                                    <option value="OR">OR - يكفي تحقق أحد الشروط</option>
                                                </select>
                                            </div>

                                            <div class="conditions-container">
                                                <!-- هنا سيتم إضافة الشروط بشكل ديناميكي -->
                                            </div>

                                            <div class="mt-2">
                                                <button type="button" class="btn btn-sm btn-primary add-condition">
                                                    <i class="fas fa-plus"></i> إضافة شرط
                                                </button>
                                                <button type="button" class="btn btn-sm btn-info add-group">
                                                    <i class="fas fa-layer-group"></i> إضافة مجموعة شروط
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="conditions" id="conditions-json"
                                        value="{{ old('conditions', '{"operator":"AND","conditions":[]}') }}">
                                    @error('conditions')
                                        <div class="text-danger mb-3">{{ $message }}</div>
                                    @enderror

                                    @if ($selectedTariffCategoryId)
                                        <input type="hidden" name="redirect_to_category" value="1">
                                    @endif

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">حفظ</button>
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
            // بيانات أنواع الشروط
            const conditionTypes = @json($conditionTypes);

            // استرجاع الشروط المحفوظة سابقًا (إن وجدت)
            let savedConditions = null;
            try {
                savedConditions = JSON.parse(document.getElementById('conditions-json').value);
            } catch (e) {
                savedConditions = {
                    operator: "AND",
                    conditions: []
                };
            }

            // تهيئة منشئ الشروط
            initConditionsBuilder(savedConditions);

            // تحديث حقل الشروط المخفي قبل إرسال النموذج
            document.getElementById('pricing-rule-form').addEventListener('submit', function() {
                const conditionsJson = buildConditionsJson();
                document.getElementById('conditions-json').value = JSON.stringify(conditionsJson);
            });

            // تهيئة منشئ الشروط
            function initConditionsBuilder(initialConditions) {
                const rootGroup = document.getElementById('root-group');

                // تعيين نوع العملية
                rootGroup.querySelector('.operator-select').value = initialConditions.operator;

                // إضافة الشروط المحفوظة
                if (initialConditions.conditions && initialConditions.conditions.length > 0) {
                    initialConditions.conditions.forEach(condition => {
                        if (condition.conditions) {
                            // مجموعة شروط
                            addConditionGroup(rootGroup.querySelector('.conditions-container'), condition);
                        } else {
                            // شرط فردي
                            addCondition(rootGroup.querySelector('.conditions-container'), condition);
                        }
                    });
                }

                // إضافة مستمعي الأحداث للأزرار
                setupEventListeners(rootGroup);
            }

            // إعداد مستمعي الأحداث
            function setupEventListeners(container) {
                // زر إضافة شرط
                container.querySelector('.add-condition').addEventListener('click', function() {
                    addCondition(container.querySelector('.conditions-container'));
                });

                // زر إضافة مجموعة شروط
                container.querySelector('.add-group').addEventListener('click', function() {
                    addConditionGroup(container.querySelector('.conditions-container'));
                });
            }

            // إضافة شرط فردي
            function addCondition(container, initialData = null) {
                const conditionItem = document.createElement('div');
                conditionItem.className = 'condition-item';

                let typeOptions = '<option value="">-- اختر نوع الشرط --</option>';
                conditionTypes.forEach(type => {
                    const selected = initialData && initialData.type === type.code ? 'selected' : '';
                    typeOptions += `<option value="${type.code}" ${selected}>${type.name}</option>`;
                });

                conditionItem.innerHTML = `
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">نوع الشرط</label>
                            <select class="form-select condition-type">
                                ${typeOptions}
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">العملية</label>
                            <select class="form-select condition-operator">
                                <option value="">-- اختر العملية --</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">القيمة</label>
                            <input type="text" class="form-control condition-value" value="${initialData ? initialData.value : ''}">
                        </div>
                        <div class="col-md-1 mb-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm remove-condition">
                                حذف
                            </button>
                        </div>
                    </div>
                `;

                container.appendChild(conditionItem);

                // إضافة مستمع حدث لزر الحذف
                conditionItem.querySelector('.remove-condition').addEventListener('click', function() {
                    container.removeChild(conditionItem);
                });

                // إضافة مستمع حدث لتغيير نوع الشرط
                const typeSelect = conditionItem.querySelector('.condition-type');
                const operatorSelect = conditionItem.querySelector('.condition-operator');

                typeSelect.addEventListener('change', function() {
                    updateOperators(typeSelect.value, operatorSelect);
                });

                // إذا كان هناك بيانات أولية، قم بتحديث العمليات وتحديد العملية المناسبة
                if (initialData && initialData.type) {
                    updateOperators(initialData.type, operatorSelect, initialData.operator);
                }
            }

            // إضافة مجموعة شروط
            function addConditionGroup(container, initialData = null) {
                const groupDiv = document.createElement('div');
                groupDiv.className = 'condition-group nested-group';

                groupDiv.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label">نوع العملية</label>
                        <select class="form-select operator-select">
                            <option value="AND" ${initialData && initialData.operator === 'AND' ? 'selected' : ''}>AND - يجب تحقق جميع الشروط</option>
                            <option value="OR" ${initialData && initialData.operator === 'OR' ? 'selected' : ''}>OR - يكفي تحقق أحد الشروط</option>
                        </select>
                    </div>
                    
                    <div class="conditions-container">
                        <!-- هنا سيتم إضافة الشروط بشكل ديناميكي -->
                    </div>
                    
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-primary add-condition">
                            <i class="fas fa-plus"></i> إضافة شرط
                        </button>
                        <button type="button" class="btn btn-sm btn-info add-group">
                            <i class="fas fa-layer-group"></i> إضافة مجموعة شروط
                        </button>
                        <button type="button" class="btn btn-sm btn-danger remove-group">
                            حذف حذف المجموعة
                        </button>
                    </div>
                `;

                container.appendChild(groupDiv);

                // إضافة مستمعي الأحداث للأزرار
                groupDiv.querySelector('.add-condition').addEventListener('click', function() {
                    addCondition(groupDiv.querySelector('.conditions-container'));
                });

                groupDiv.querySelector('.add-group').addEventListener('click', function() {
                    addConditionGroup(groupDiv.querySelector('.conditions-container'));
                });

                groupDiv.querySelector('.remove-group').addEventListener('click', function() {
                    container.removeChild(groupDiv);
                });

                // إضافة الشروط المحفوظة إذا وجدت
                if (initialData && initialData.conditions && initialData.conditions.length > 0) {
                    initialData.conditions.forEach(condition => {
                        if (condition.conditions) {
                            // مجموعة شروط
                            addConditionGroup(groupDiv.querySelector('.conditions-container'), condition);
                        } else {
                            // شرط فردي
                            addCondition(groupDiv.querySelector('.conditions-container'), condition);
                        }
                    });
                }
            }

            // تحديث قائمة العمليات بناءً على نوع الشرط
            function updateOperators(typeCode, operatorSelect, selectedOperator = null) {
                operatorSelect.innerHTML = '<option value="">-- اختر العملية --</option>';

                const conditionType = conditionTypes.find(type => type.code === typeCode);
                if (!conditionType) return;

                let operators = [];
                try {
                    operators = JSON.parse(conditionType.available_operators);
                } catch (e) {
                    operators = [];
                }

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

                operators.forEach(op => {
                    const label = operatorLabels[op] || op;
                    const selected = selectedOperator === op ? 'selected' : '';
                    operatorSelect.innerHTML += `<option value="${op}" ${selected}>${label}</option>`;
                });
            }

            // بناء كائن JSON للشروط
            function buildConditionsJson() {
                const rootGroup = document.getElementById('root-group');
                return buildGroupJson(rootGroup);
            }

            // بناء كائن JSON لمجموعة شروط
            function buildGroupJson(groupElement) {
                const operator = groupElement.querySelector('.operator-select').value;
                const conditions = [];

                // جمع الشروط الفردية والمجموعات
                const conditionsContainer = groupElement.querySelector('.conditions-container');
                conditionsContainer.childNodes.forEach(child => {
                    if (child.classList && child.classList.contains('condition-item')) {
                        // شرط فردي
                        const typeSelect = child.querySelector('.condition-type');
                        const operatorSelect = child.querySelector('.condition-operator');
                        const valueInput = child.querySelector('.condition-value');

                        if (typeSelect.value && operatorSelect.value) {
                            conditions.push({
                                type: typeSelect.value,
                                operator: operatorSelect.value,
                                value: valueInput.value
                            });
                        }
                    } else if (child.classList && child.classList.contains('condition-group')) {
                        // مجموعة شروط
                        conditions.push(buildGroupJson(child));
                    }
                });

                return {
                    operator: operator,
                    conditions: conditions
                };
            }
        });
    </script>



</x-app-layout>
