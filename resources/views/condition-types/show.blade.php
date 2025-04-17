<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('fixed_fees.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
        </div>
        <div class="container-fluid mt-2">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">تفاصيل نوع الشرط: {{ $conditionType->name }}</h5>
                                <div>
                                    <a href="{{ route('condition-types.edit', $conditionType) }}"
                                        class="btn btn-warning btn-sm">
                                        تعديل تعديل
                                    </a>
                                    <a href="{{ route('condition-types.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-right"></i> العودة للقائمة
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <h6 class="fw-bold">معلومات نوع الشرط</h6>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">الكود</th>
                                                <td>{{ $conditionType->code }}</td>
                                            </tr>
                                            <tr>
                                                <th>الاسم</th>
                                                <td>{{ $conditionType->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>نوع البيانات</th>
                                                <td>{{ $dataTypes[$conditionType->data_type] ?? $conditionType->data_type }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>نوع العمليات</th>
                                                <td>{{ $operatorTypes[$conditionType->operator_type] ?? $conditionType->operator_type }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>العمليات المتاحة</th>
                                                <td>
                                                    @php
                                                        $availableOperators =
                                                            json_decode($conditionType->available_operators, true) ??
                                                            [];
                                                    @endphp

                                                    @if (count($availableOperators) > 0)
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @foreach ($availableOperators as $operator)
                                                                <span class="badge bg-primary">
                                                                    {{ $operatorLabels[$operator] ?? $operator }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-muted">لا توجد عمليات متاحة</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>الحالة</th>
                                                <td>
                                                    <span
                                                        class="badge {{ $conditionType->active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $conditionType->active ? 'مفعل' : 'معطل' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>تاريخ الإنشاء</th>
                                                <td>{{ $conditionType->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>آخر تحديث</th>
                                                <td>{{ $conditionType->updated_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="fw-bold">استخدام نوع الشرط في قواعد التسعير</h6>
                                        <div class="p-3 bg-light rounded">
                                            <p>يمكن استخدام هذا النوع من الشروط في قواعد التسعير للتحقق من:</p>
                                            <ul>
                                                <li>
                                                    <strong>{{ $conditionType->name }}</strong>:
                                                    @switch($conditionType->data_type)
                                                        @case('string')
                                                            قيمة نصية مثل
                                                            "{{ $conditionType->code === 'vessel_type' ? 'CARGO' : 'قيمة نصية' }}"
                                                        @break

                                                        @case('number')
                                                            قيمة رقمية مثل
                                                            {{ $conditionType->code === 'gt_size' ? '5000' : '123' }}
                                                        @break

                                                        @case('boolean')
                                                            قيمة منطقية (نعم/لا)
                                                        @break

                                                        @case('date')
                                                            تاريخ مثل "2025-01-01"
                                                        @break

                                                        @case('array')
                                                            قائمة من القيم مثل ["قيمة1", "قيمة2"]
                                                        @break

                                                        @default
                                                            قيمة من نوع {{ $conditionType->data_type }}
                                                    @endswitch
                                                </li>
                                            </ul>

                                            <p>أمثلة على استخدام هذا الشرط في قواعد التسعير:</p>
                                            <ul>
                                                @php
                                                    $exampleValue = '';
                                                    switch ($conditionType->code) {
                                                        case 'vessel_type':
                                                            $exampleValue = 'CARGO';
                                                            break;
                                                        case 'gt_size':
                                                            $exampleValue = '5000';
                                                            break;
                                                        case 'loa':
                                                            $exampleValue = '150';
                                                            break;
                                                        case 'call_type':
                                                            $exampleValue = 'IMPORT';
                                                            break;
                                                        default:
                                                            $exampleValue =
                                                                $conditionType->data_type === 'string' ? 'قيمة' : '123';
                                                    }
                                                @endphp

                                                @foreach (array_slice(json_decode($conditionType->available_operators, true) ?? [], 0, 3) as $operator)
                                                    <li>
                                                        <strong>{{ $conditionType->name }}</strong>
                                                        {{ $operatorLabels[$operator] ?? $operator }}
                                                        <span class="badge bg-secondary">{{ $exampleValue }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
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
</x-app-layout>
