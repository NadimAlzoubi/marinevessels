<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('fixed_fees.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
        </div>
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

                .test-result {
                    display: none;
                    margin-top: 15px;
                    padding: 15px;
                    border-radius: 5px;
                }

                .test-result.success {
                    background-color: #d4edda;
                    border: 1px solid #c3e6cb;
                    color: #155724;
                }

                .test-result.error {
                    background-color: #f8d7da;
                    border: 1px solid #f5c6cb;
                    color: #721c24;
                }
            </style>

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">تفاصيل قاعدة التسعير: {{ $pricingRule->name }}</h5>
                                <div>
                                    <a href="{{ route('pricing-rules.edit', $pricingRule) }}"
                                        class="btn btn-warning btn-sm">
                                        تعديل تعديل
                                    </a>
                                    <a href="{{ route('pricing-rules.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-right"></i> العودة للقائمة
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">معلومات قاعدة التسعير</h6>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">الاسم</th>
                                                <td>{{ $pricingRule->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>فئة التعريفة</th>
                                                <td>
                                                    <a
                                                        href="{{ route('tariff-categories.show', $pricingRule->tariffCategory) }}">
                                                        {{ $pricingRule->tariffCategory->code }} -
                                                        {{ $pricingRule->tariffCategory->name }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>الخدمة</th>
                                                <td>
                                                    <a
                                                        href="{{ route('services.show', $pricingRule->tariffCategory->service) }}">
                                                        {{ $pricingRule->tariffCategory->service->name }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>السعر الأساسي</th>
                                                <td>{{ number_format($pricingRule->rate, 2) }} درهم</td>
                                            </tr>
                                            <tr>
                                                <th>وحدة القياس</th>
                                                <td>{{ $pricingRule->tariffCategory->unit_of_measurement }}</td>
                                            </tr>
                                            <tr>
                                                <th>الأولوية</th>
                                                <td>{{ $pricingRule->priority }}</td>
                                            </tr>
                                            <tr>
                                                <th>تاريخ السريان</th>
                                                <td>
                                                    @if ($pricingRule->effective_from || $pricingRule->effective_to)
                                                        {{ $pricingRule->effective_from ? $pricingRule->effective_from->format('Y-m-d') : 'غير محدد' }}
                                                        إلى
                                                        {{ $pricingRule->effective_to ? $pricingRule->effective_to->format('Y-m-d') : 'غير محدد' }}
                                                    @else
                                                        غير محدد
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>الحالة</th>
                                                <td>
                                                    <span
                                                        class="badge {{ $pricingRule->active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $pricingRule->active ? 'مفعل' : 'معطل' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>تاريخ الإنشاء</th>
                                                <td>{{ $pricingRule->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>آخر تحديث</th>
                                                <td>{{ $pricingRule->updated_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">اختبار القاعدة</h6>
                                        <div class="p-3 bg-light rounded">
                                            <form id="test-rule-form"
                                                action="{{ route('pricing-rules.test', $pricingRule->id) }}"
                                                method="POST">
                                                @csrf <div class="mb-3">
                                                    <label class="form-label">نوع السفينة</label>
                                                    <select class="form-select" name="vessel_type">
                                                        <option value="">-- اختر نوع السفينة --</option>
                                                        <option value="CARGO">سفينة بضائع</option>
                                                        <option value="PASSENGER">سفينة ركاب</option>
                                                        <option value="TANKER">ناقلة</option>
                                                        <option value="CONTAINER">حاوية</option>
                                                        <option value="OTHER">أخرى</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">الحجم الإجمالي (GT)</label>
                                                    <input type="number" class="form-control" name="gt_size"
                                                        min="0">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">الطول الكلي (LOA) بالمتر</label>
                                                    <input type="number" class="form-control" name="loa"
                                                        min="0">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">مدة البقاء (بالأيام)</label>
                                                    <input type="number" class="form-control" name="stay_duration"
                                                        min="0">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">ساعات الخدمة</label>
                                                    <input type="number" class="form-control" name="service_hours"
                                                        min="0">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">الكمية</label>
                                                    <input type="number" class="form-control" name="quantity"
                                                        min="1" value="1">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">نوع الزيارة</label>
                                                    <select class="form-select" name="call_type">
                                                        <option value="">-- اختر نوع الزيارة --</option>
                                                        <option value="IMPORT">استيراد</option>
                                                        <option value="EXPORT">تصدير</option>
                                                        <option value="TRANSIT">عبور</option>
                                                    </select>
                                                </div>

                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary">اختبار
                                                        القاعدة</button>
                                                </div>
                                            </form>

                                            <div id="test-result" class="test-result">
                                                <!-- نتيجة الاختبار ستظهر هنا -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="fw-bold">شروط تطبيق القاعدة</h6>
                                        <div id="conditions-display" class="mb-4">
                                            <!-- عرض الشروط سيتم إنشاؤه ديناميكيًا -->
                                        </div>
                                    </div>
                                </div>
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

            // بيانات الشروط
            const conditions = @json($pricingRule->conditions);

            // عرض الشروط
            renderConditions(conditions);

            // إنشاء نموذج اختبار القاعدة
            createTestForm(conditions);

            // معالجة نموذج الاختبار
            document.getElementById('test-rule-form').addEventListener('submit', function(e) {
                e.preventDefault();
                testRule();
            });

            // عرض الشروط
            function renderConditions(conditionsData) {
                const conditionsViewer = document.getElementById('conditions-viewer');

                if (!conditionsData || !conditionsData.conditions || conditionsData.conditions.length === 0) {
                    conditionsViewer.innerHTML =
                        '<div class="alert alert-info">لا توجد شروط محددة لهذه القاعدة.</div>';
                    return;
                }

                conditionsViewer.innerHTML = renderConditionGroup(conditionsData);
            }

            // عرض مجموعة شروط
            function renderConditionGroup(group, level = 0) {
                const isRoot = level === 0;
                const groupClass = isRoot ? 'condition-group' : 'condition-group nested-group';

                let html = `<div class="${groupClass}">`;

                // عنوان المجموعة
                html += `
                <div class="mb-3">
                    <h6 class="mb-2">${isRoot ? 'الشروط الرئيسية' : 'مجموعة شروط'}</h6>
                    <div class="badge bg-primary mb-2">
                        ${group.operator === 'AND' ? 'يجب تحقق جميع الشروط (AND)' : 'يكفي تحقق أحد الشروط (OR)'}
                    </div>
                </div>
            `;

                // الشروط
                if (group.conditions && group.conditions.length > 0) {
                    group.conditions.forEach((condition, index) => {
                        if (condition.conditions) {
                            // مجموعة شروط
                            html += renderConditionGroup(condition, level + 1);
                        } else {
                            // شرط فردي
                            html += renderConditionItem(condition, index);
                        }
                    });
                } else {
                    html += '<div class="alert alert-warning">لا توجد شروط في هذه المجموعة.</div>';
                }

                html += '</div>';
                return html;
            }

            // عرض شرط فردي
            function renderConditionItem(condition, index) {
                const conditionType = conditionTypes.find(type => type.code === condition.type);

                if (!conditionType) {
                    return `<div class="alert alert-danger">نوع شرط غير معروف: ${condition.type}</div>`;
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

                const operatorLabel = operatorLabels[condition.operator] || condition.operator;

                return `
                <div class="condition-item">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>نوع الشرط:</strong> ${conditionType.name}
                        </div>
                        <div class="col-md-4">
                            <strong>العملية:</strong> ${operatorLabel}
                        </div>
                        <div class="col-md-4">
                            <strong>القيمة:</strong> ${formatConditionValue(condition.value, condition.operator)}
                        </div>
                    </div>
                </div>
            `;
            }

            // تنسيق قيمة الشرط
            function formatConditionValue(value, operator) {
                if (operator === 'in' || operator === 'not_in') {
                    try {
                        const values = JSON.parse(value);
                        if (Array.isArray(values)) {
                            return values.join(', ');
                        }
                    } catch (e) {
                        // إذا لم يكن JSON صالح، استخدم القيمة كما هي
                    }
                } else if (operator === 'between') {
                    try {
                        const values = JSON.parse(value);
                        if (Array.isArray(values) && values.length === 2) {
                            return `${values[0]} - ${values[1]}`;
                        }
                    } catch (e) {
                        // إذا لم يكن JSON صالح، استخدم القيمة كما هي
                    }
                }

                return value;
            }

            // إنشاء نموذج اختبار القاعدة
            function createTestForm(conditionsData) {
                const testConditionsContainer = document.getElementById('test-conditions-container');
                const uniqueConditionTypes = extractUniqueConditionTypes(conditionsData);

                if (uniqueConditionTypes.length === 0) {
                    testConditionsContainer.innerHTML =
                        '<div class="alert alert-info">لا توجد شروط لاختبارها.</div>';
                    return;
                }

                let html = '<div class="row">';

                uniqueConditionTypes.forEach(typeCode => {
                    const conditionType = conditionTypes.find(type => type.code === typeCode);

                    if (!conditionType) return;

                    html += `
                    <div class="col-md-6 mb-3">
                        <label for="test_${typeCode}" class="form-label">${conditionType.name}</label>
                        <input type="text" class="form-control" id="test_${typeCode}" name="test_values[${typeCode}]">
                        <small class="text-muted">
                            ${getConditionTypeHint(conditionType)}
                        </small>
                    </div>
                `;
                });

                html += '</div>';
                testConditionsContainer.innerHTML = html;
            }

            // استخراج أنواع الشروط الفريدة
            function extractUniqueConditionTypes(conditionsData) {
                const types = new Set();

                function extractTypes(group) {
                    if (!group || !group.conditions) return;

                    group.conditions.forEach(condition => {
                        if (condition.conditions) {
                            // مجموعة شروط
                            extractTypes(condition);
                        } else if (condition.type) {
                            // شرط فردي
                            types.add(condition.type);
                        }
                    });
                }

                extractTypes(conditionsData);
                return Array.from(types);
            }

            // الحصول على تلميح لنوع الشرط
            function getConditionTypeHint(conditionType) {
                switch (conditionType.data_type) {
                    case 'string':
                        return 'أدخل نصًا، مثل: CARGO';
                    case 'number':
                        return 'أدخل رقمًا، مثل: 5000';
                    case 'boolean':
                        return 'أدخل true أو false';
                    case 'date':
                        return 'أدخل تاريخًا بتنسيق YYYY-MM-DD';
                    case 'array':
                        return 'أدخل قائمة مفصولة بفواصل، مثل: قيمة1,قيمة2,قيمة3';
                    default:
                        return '';
                }
            }

            // اختبار القاعدة
            function testRule() {
                const form = document.getElementById('test-rule-form');
                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const testResult = document.getElementById('test-result');
                        const testResultValue = document.getElementById('test-result-value');

                        testResult.classList.remove('d-none');

                        if (data.success) {
                            testResultValue.textContent = parseFloat(data.price).toFixed(2);
                            testResultValue.className = 'text-success fw-bold';
                        } else {
                            testResultValue.textContent = 'خطأ';
                            testResultValue.className = 'text-danger fw-bold';
                            alert('حدث خطأ: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('حدث خطأ أثناء الاختبار. يرجى المحاولة مرة أخرى.');
                    });
            }
        });
    </script>

</x-app-layout>
