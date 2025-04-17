<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('fixed_fees.create') }}" class="btn btn-success mb-3 mt-3">Add New Fixed Fee
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
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">إدارة قواعد التسعير</h5>
                                <a href="{{ route('pricing-rules.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> إضافة قاعدة تسعير جديدة
                                </a>
                            </div>

                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>الاسم</th>
                                                <th>فئة التعريفة</th>
                                                <th>السعر الأساسي</th>
                                                <th>الأولوية</th>
                                                <th>تاريخ السريان</th>
                                                <th>الحالة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($pricingRules as $rule)
                                                <tr>
                                                    <td>{{ $rule->name }}</td>
                                                    <td>
                                                        <a
                                                            href="{{ route('tariff-categories.show', $rule->tariffCategory) }}">
                                                            {{ $rule->tariffCategory->code }} -
                                                            {{ $rule->tariffCategory->name }}
                                                        </a>
                                                        <div class="small text-muted">
                                                            {{ $rule->tariffCategory->service->name }}
                                                        </div>
                                                    </td>
                                                    <td>{{ number_format($rule->rate, 2) }}</td>
                                                    <td>{{ $rule->priority }}</td>
                                                    <td>
                                                        @if ($rule->effective_from || $rule->effective_to)
                                                            {{ $rule->effective_from ? $rule->effective_from->format('Y-m-d') : 'غير محدد' }}
                                                            إلى
                                                            {{ $rule->effective_to ? $rule->effective_to->format('Y-m-d') : 'غير محدد' }}
                                                        @else
                                                            غير محدد
                                                        @endif
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
                                                                action="{{ route('pricing-rules.toggle-active', $rule) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="btn {{ $rule->active ? 'btn-secondary' : 'btn-success' }} btn-sm"
                                                                    title="{{ $rule->active ? 'تعطيل' : 'تفعيل' }}">
                                                                    {{ $rule->active ? 'تعطيل' : 'تفعيل' }}
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('pricing-rules.destroy', $rule) }}"
                                                                method="POST" class="d-inline delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm"
                                                                    title="حذف">
                                                                    حذف
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">لا توجد قواعد تسعير متاحة
                                                    </td>
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
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأكيد الحذف
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (confirm('هل أنت متأكد من رغبتك في حذف قاعدة التسعير هذه؟')) {
                        this.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
