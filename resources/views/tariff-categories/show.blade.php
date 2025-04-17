<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('fixed_fees.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
        </div>
        <div class="container-fluid mt-2">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">تفاصيل فئة التعريفة: {{ $tariffCategory->name }}</h5>
                                <div>
                                    <a href="{{ route('pricing-rules.create', ['tariff_category_id' => $tariffCategory->id]) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> إضافة قاعدة تسعير
                                    </a>
                                    <a href="{{ route('tariff-categories.edit', $tariffCategory) }}"
                                        class="btn btn-warning btn-sm">
                                        تعديل تعديل
                                    </a>
                                    <a href="{{ route('tariff-categories.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-right"></i> العودة للقائمة
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">معلومات فئة التعريفة</h6>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">الكود</th>
                                                <td>{{ $tariffCategory->code }}</td>
                                            </tr>
                                            <tr>
                                                <th>الاسم</th>
                                                <td>{{ $tariffCategory->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>الخدمة</th>
                                                <td>
                                                    <a href="{{ route('services.show', $tariffCategory->service) }}">
                                                        {{ $tariffCategory->service->name }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>وحدة القياس</th>
                                                <td>{{ $tariffCategory->unit_of_measurement }}</td>
                                            </tr>
                                            <tr>
                                                <th>الحالة</th>
                                                <td>
                                                    <span
                                                        class="badge {{ $tariffCategory->active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $tariffCategory->active ? 'مفعل' : 'معطل' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>تاريخ الإنشاء</th>
                                                <td>{{ $tariffCategory->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>آخر تحديث</th>
                                                <td>{{ $tariffCategory->updated_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">الوصف</h6>
                                        <div class="p-3 bg-light rounded">
                                            {{ $tariffCategory->description ?: 'لا يوجد وصف متاح' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold">قواعد التسعير المرتبطة</h6>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>الاسم</th>
                                                        <th>السعر الأساسي</th>
                                                        <th>الأولوية</th>
                                                        <th>تاريخ السريان</th>
                                                        <th>تاريخ الانتهاء</th>
                                                        <th>الحالة</th>
                                                        <th>الإجراءات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($pricingRules as $rule)
                                                        <tr>
                                                            <td>{{ $rule->name }}</td>
                                                            <td>{{ number_format($rule->rate, 2) }}</td>
                                                            <td>{{ $rule->priority }}</td>
                                                            <td>{{ $rule->effective_from ? $rule->effective_from->format('Y-m-d') : 'غير محدد' }}
                                                            </td>
                                                            <td>{{ $rule->effective_to ? $rule->effective_to->format('Y-m-d') : 'غير محدد' }}
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge {{ $rule->active ? 'bg-success' : 'bg-danger' }}">
                                                                    {{ $rule->active ? 'مفعل' : 'معطل' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('pricing-rules.show', $rule) }}"
                                                                        class="btn btn-info btn-sm" title="عرض">
                                                                        عرض
                                                                    </a>
                                                                    <a href="{{ route('pricing-rules.edit', $rule) }}"
                                                                        class="btn btn-warning btn-sm" title="تعديل">
                                                                        تعديل
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('pricing-rules.destroy', $rule) }}"
                                                                        method="POST" class="d-inline delete-form">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-danger btn-sm"
                                                                            title="حذف">
                                                                            حذف
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center">لا توجد قواعد تسعير
                                                                مرتبطة بفئة التعريفة هذه</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $pricingRules->links() }}
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
