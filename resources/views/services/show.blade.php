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
                                <h5 class="mb-0">تفاصيل الخدمة: {{ $service->name }}</h5>
                                <div>
                                    <a href="{{ route('services.edit', $service) }}" class="btn btn-warning btn-sm">
                                        تعديل
                                    </a>
                                    <a href="{{ route('services.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-right"></i> العودة للقائمة
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">معلومات الخدمة</h6>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">الكود</th>
                                                <td>{{ $service->code }}</td>
                                            </tr>
                                            <tr>
                                                <th>الاسم</th>
                                                <td>{{ $service->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>الحالة</th>
                                                <td>
                                                    <span
                                                        class="badge {{ $service->active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $service->active ? 'مفعل' : 'معطل' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>تاريخ الإنشاء</th>
                                                <td>{{ $service->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>آخر تحديث</th>
                                                <td>{{ $service->updated_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold">الوصف</h6>
                                        <div class="p-3 bg-light rounded">
                                            {{ $service->description ?: 'لا يوجد وصف متاح' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold">فئات التعريفة المرتبطة</h6>
                                            <a href="{{ route('tariff-categories.create', ['service_id' => $service->id]) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus"></i> إضافة فئة تعريفة
                                            </a>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>الكود</th>
                                                        <th>الاسم</th>
                                                        <th>وحدة القياس</th>
                                                        <th>الحالة</th>
                                                        <th>قواعد التسعير</th>
                                                        <th>الإجراءات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($tariffCategories as $category)
                                                        <tr>
                                                            <td>{{ $category->code }}</td>
                                                            <td>{{ $category->name }}</td>
                                                            <td>{{ $category->unit_of_measurement }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge {{ $category->active ? 'bg-success' : 'bg-danger' }}">
                                                                    {{ $category->active ? 'مفعل' : 'معطل' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-info">
                                                                    {{ $category->pricingRules()->count() }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('tariff-categories.show', $category) }}"
                                                                        class="btn btn-info btn-sm" title="عرض">
                                                                        عرض
                                                                    </a>
                                                                    <a href="{{ route('tariff-categories.edit', $category) }}"
                                                                        class="btn btn-warning btn-sm" title="تعديل">
                                                                        تعديل
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('tariff-categories.destroy', $category) }}"
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
                                                            <td colspan="6" class="text-center">لا توجد فئات تعريفة
                                                                مرتبطة بهذه الخدمة</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $tariffCategories->links() }}
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
