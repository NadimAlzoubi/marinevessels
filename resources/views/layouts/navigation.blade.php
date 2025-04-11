<!-- Sidebar for navigation -->
<div class="sidebar">
    <div class="logo-details">
        <!-- Icon and logo name -->
        <i class='bx bxs-ship icon'></i>
        <div class="logo_name">Marine Valley</div>
        <i class='bx bx-menu' id="btn"></i> <!-- Menu button to toggle sidebar -->
    </div>
    <ul class="nav-list">
        <!-- List of navigation items -->
        <li>
            <a href="{{ route('dashboard') }}">
                <i class='bx bx-grid-alt'></i>
                <span class="links_name">{{ __('Dashboard') }}</span>
            </a>
            <span class="tooltip">{{ __('Dashboard') }}</span>
        </li>

        <li>
            <a href="{{ route('vessels.index') }}">
                <i class='bx bxs-ship'></i>
                <span class="links_name">{{ __('Vessels') }}</span>
            </a>
            <span class="tooltip">{{ __('Vessels') }}</span>
        </li>
        {{--  --}}
        <li class="dropdown">
            <a href="#" class="dropdown-btn">
                <i class='bx bx-money'></i>
                <span class="links_name">{{ __('Financial') }}</span>
                <i class='bx bx-chevron-down arrow'></i> <!-- أيقونة توسيع -->
            </a>
            <ul class="dropdown-content">
                <li>
                    <a class="text-sm" href="{{ route('invoices.index') }}">
                        <span class="branch-line"></span> <!-- خط فرعي -->
                        <i class="bx bx-receipt"></i>{{ __('Invoices') }}
                    </a>
                </li>

                {{-- @admin
                @endadmin

                @editor
                @endeditor

                @contributor
                @endcontributor

                @guestuser
                @endguestuser

                @notguestuser
                @endnotguestuser --}}

                @role(['admin', 'editor'])
                    <li>
                        <a class="text-sm" href="{{ route('fee_categories.index') }}">
                            <span class="branch-line"></span> <!-- خط فرعي -->
                            <i class="bx bx-list-ul"></i>{{ __('Fee Categories') }}
                        </a>
                    </li>
                    <li>
                        <a class="text-sm" href="{{ route('fixed_fees.index') }}">
                            <span class="branch-line"></span> <!-- خط فرعي -->
                            <i class="bx bx-money"></i>{{ __('Fixed Fees') }}
                        </a>
                    </li>
                @endrole
            </ul>
            <span class="tooltip">{{ __('Financial') }}</span>
        </li>

        {{--  --}}


        {{--  --}}

        @admin
            <li class="dropdown">
                <a href="#" class="dropdown-btn">
                    <i class='bx bx-cog'></i>
                    <span class="links_name">{{ __('Settings') }}</span>
                    <i class='bx bx-chevron-down arrow'></i> <!-- أيقونة توسيع -->
                </a>
                <ul class="dropdown-content">
                    <li>
                        <a class="text-sm" href="{{ route('admin.users.index') }}">
                            <span class="branch-line"></span> <!-- خط فرعي -->
                            <i class='bx bxs-user-detail'></i>{{ __('Manage Users') }}
                        </a>
                    </li>


                </ul>
                <span class="tooltip">{{ __('Settings') }}</span>
            </li>
        @endadmin

        {{--  --}}


















        <!-- Profile section -->
        <li class="profile">
            <div class="profile-details">
                <img src="{{ asset('images/profile1.png') }}" alt="profileImg">
                <div class="name_job">
                    <div class="name">
                        <a href="{{ route('profile.edit') }}">
                            {{ Auth::user()->name }}
                        </a>
                    </div>
                    <div class="job capitalize">{{ '@' . Auth::user()->role }}</div>
                </div>
            </div>
            <!-- Authentication -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class='bx bx-log-out' id="log_out"></i>
                </x-responsive-nav-link>
            </form>
        </li>
    </ul>
</div>

<script>
    document.querySelectorAll(".dropdown-btn").forEach(button => {
        button.addEventListener("click", function(e) {
            e.preventDefault(); // منع التنقل الفوري
            let parent = this.parentElement;
            let dropdown = parent.querySelector(".dropdown-content");

            if (parent.classList.contains("active")) {
                dropdown.style.maxHeight = null; // إغلاق بسلاسة
            } else {
                dropdown.style.maxHeight = dropdown.scrollHeight + "px"; // فتح بسلاسة
            }

            parent.classList.toggle("active");
        });
    });
</script>
