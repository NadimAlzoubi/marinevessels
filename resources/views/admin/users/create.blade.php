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
                            <h5 class="mb-0">Add User</h5>
                        </div>
                        <div class="card-body">
                            <form id="createUserForm" action="{{ route('admin.users.index') }}" method="POST">
                                @csrf
                                <input type="hidden" id="user_id" name="user_id">
                                <!-- حقل مخفي لتخزين ID المستخدم الذي  يتم تعديله -->

                                <div class="grid-container">
                                    <div class="grid-item">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                                    </div>
                                    <div class="grid-item">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                                    </div>
                                    <div class="grid-item">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}">
                                    </div> 
                                    <div class="grid-item">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}">
                                    </div>
                                    <div class="grid-item">
                                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" value="{{ old('password_confirmation') }}">
                                    </div>                                    
                                    <div class="grid-item">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-select" id="role" name="role">
                                            <option value="guest" {{ old('role') == 'guest' ? 'selected' : '' }}>Guest</option>
                                            <option value="contributor" {{ old('role') == 'contributor' ? 'selected' : '' }}>Contributor</option>
                                            <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Editor</option>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </div>
                                    <div class="grid-item">
                                        <label for="active" class="form-label">Status</label>
                                        <select class="form-select" id="active" name="active">
                                            <option value="1" {{ old('active') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('active') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>                                    
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                    <button type="submit" id="submit-button" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</x-app-layout>
