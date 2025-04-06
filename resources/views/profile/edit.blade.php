<x-app-layout>
    <section class="home-section">
        <div class="text">Profile</div>
        <div class="container-fluid">
            @if (session('status') === 'email-verified')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ __('Your email has been verified successfully!') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <!-- تحقق إذا كانت المصادقة الثنائية مفعلة -->
                        @if (auth()->user()->hasTwoFactorEnabled())
                            <!-- تعطيل المصادقة الثنائية -->
                            <form method="POST" action="{{ route('two-factor.disable') }}">
                                @csrf
                                @method('DELETE')

                                {{-- <div> --}}
                                {{-- {!! auth()->user()->twoFactorQrCodeSvg() !!} --}}
                                {{-- </div> --}}


                                <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    Disable 2FA
                                </button>
                            </form>
                        @else
                            <!-- تفعيل المصادقة الثنائية -->
                            <form method="POST" action="{{ route('two-factor.enable') }}">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Enable 2FA
                                </button>
                            </form>
                        @endif
                    </div>
                </div>


                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
