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
                                <h5 class="mb-0">إدارة الخدمات</h5>
                                <a href="{{ route('services.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> إضافة خدمة جديدة
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
                                                <th>الكود</th>
                                                <th>الاسم</th>
                                                <th>الوصف</th>
                                                <th>الحالة</th>
                                                <th>فئات التعريفة</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($services as $service)
                                                <tr>
                                                    <td>{{ $service->code }}</td>
                                                    <td>{{ $service->name }}</td>
                                                    <td>{{ Str::limit($service->description, 50) }}</td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $service->active ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $service->active ? 'مفعل' : 'معطل' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            {{ $service->tariffCategories()->count() }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('services.show', $service) }}"
                                                                class="btn btn-info btn-sm" title="عرض">
                                                                عرض
                                                            </a>
                                                            <a href="{{ route('services.edit', $service) }}"
                                                                class="btn btn-warning btn-sm" title="تعديل">
                                                                تعديل
                                                            </a>
                                                            <form
                                                                action="{{ route('services.toggle-active', $service) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="btn {{ $service->active ? 'btn-secondary' : 'btn-success' }} btn-sm"
                                                                    title="{{ $service->active ? 'تعطيل' : 'تفعيل' }}">
                                                                    {{ $service->active ? 'تعطيل' : 'تفعيل' }}
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('services.destroy', $service) }}"
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
                                                    <td colspan="6" class="text-center">لا توجد خدمات متاحة</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center mt-3">
                                    {{ $services->links() }}
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
                    if (confirm('هل أنت متأكد من رغبتك في حذف هذه الخدمة؟')) {
                        this.submit();
                    }
                });
            });
        });
    </script>

</x-app-layout>
