<x-app-layout>
    <section class="home-section">
        <div class="container-fluid">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary mb-3 mt-3">Back</a>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
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
            <div class="row" id="user-form">
                <div class="col-lg-12 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Edit User</h5>
                        </div>
                        <div class="card-body">
                            <form id="editUserForm" action="{{ route('admin.users.update', $user->id) }}"
                                method="POST">
                                @csrf
                                @method('PATCH') <!-- لأننا نقوم بتحديث بعض الحقول فقط -->
                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name', $user->name) }}">
                                    </div>
                                    <div class="grid-item">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', $user->email) }}">
                                    </div>
                                    <div class="grid-item">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username"
                                            value="{{ old('username', $user->username) }}">
                                    </div>
                                    <div class="grid-item">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        <small class="text-danger">Leave blank to keep current
                                            password.</small>
                                    </div>
                                    <div class="grid-item">
                                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation">
                                    </div>
                                    @php
                                        $isLastAdmin =
                                            $user->role === 'admin' &&
                                            \App\Models\User::where('role', 'admin')->count() === 1;
                                    @endphp

                                    <div class="grid-item">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-select" id="role" name="role"
                                            {{ $isLastAdmin ? 'disabled' : '' }}>
                                            <option value="guest"
                                                {{ old('role', $user->role) == 'guest' ? 'selected' : '' }}>Guest
                                            </option>
                                            <option value="contributor"
                                                {{ old('role', $user->role) == 'contributor' ? 'selected' : '' }}>
                                                Contributor</option>
                                            <option value="editor"
                                                {{ old('role', $user->role) == 'editor' ? 'selected' : '' }}>Editor
                                            </option>
                                            <option value="admin"
                                                {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin
                                            </option>
                                        </select>
                                        @if ($isLastAdmin)
                                            <small class="text-danger">Last Admin role cannot be changed.</small>
                                            <!-- حقل مخفي لإرسال قيمة role في حالة تعطيل الحقل -->
                                            <input type="hidden" name="role"
                                                value="{{ old('role', $user->role) }}">
                                        @endif
                                    </div>

                                    <div class="grid-item">
                                        <label for="active" class="form-label">Status</label>
                                        <select class="form-select" id="active" name="active"
                                            {{ $isLastAdmin ? 'disabled' : '' }}>
                                            <option value="1"
                                                {{ old('active', $user->active) == '1' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0"
                                                {{ old('active', $user->active) == '0' ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                        @if ($isLastAdmin)
                                            <small class="text-danger">Last Admin status cannot be changed.</small>
                                            <!-- حقل مخفي لإرسال قيمة active في حالة تعطيل الحقل -->
                                            <input type="hidden" name="active"
                                                value="{{ old('active', $user->active) }}">
                                        @endif
                                    </div>

                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                    <button type="submit" id="submit-button" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
