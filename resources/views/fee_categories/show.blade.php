<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            {{-- <span>User Details</span> --}}
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
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

        <div class="container-fluid mt-2">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">User Details</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>ID:</strong> {{ $user->id }}</p>
                            <p><strong>Name:</strong> {{ $user->name }}</p>
                            <p><strong>Username:</strong> {{ $user->username }}</p>
                            <p><strong>Password:</strong> {{ $user->password }}</p>
                            <p><strong>Email verified at:</strong>
                                {{ \Carbon\Carbon::parse($user->email_verified_at)->format('d/m/Y h:i A') }}
                            </p>
                            <p><strong>Status:</strong>
                                @if ($user->active == 0)
                                    Inactive
                                @elseif($user->active == 1)
                                    Active
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>



<x-app-layout>
    <div class="container-fluid">
        <h1>تفاصيل فئة الرسوم</h1>
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $category->category_name }}</h4>
                <p class="card-text">{{ $category->description }}</p>
                <a href="{{ route('fee_categories.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>
        </div>
    </div>
</x-app-layout>