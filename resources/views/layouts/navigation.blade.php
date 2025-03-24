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
                <span class="links_name">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>

        <li>
            <a href="{{ route('vessels.index') }}">
                <i class='bx bxs-ship'></i>
                <span class="links_name">Vessels</span>
            </a>
            <span class="tooltip">Vessels</span>
        </li>
        
        <li>
            <a href="{{ route('fee_categories.index') }}">
                <i class="bx bx-list-ul"></i>
                <span class="links_name">Fee Categories</span>
            </a>
            <span class="tooltip">Fee Categories</span>
        </li>

        <li>
            <a href="{{ route('fixed_fees.index') }}">
                <i class="bx bx-money"></i>
                <span class="links_name">Fixed Fees</span>
            </a>
            <span class="tooltip">Fixed Fees</span>
        </li>

        <li>
            <a href="{{ route('invoices.index') }}">
                <i class="bx bx-receipt"></i>
                <span class="links_name">Invoices</span>
            </a>
            <span class="tooltip">Invoices</span>
        </li>

        <li>
            <a href="{{ route('vessels.index') }}">
                <i class='bx bx-cog'></i>
                <span class="links_name">Settings</span>
            </a>
            <span class="tooltip">Settings</span>
        </li>
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
                    <div class="job">@Admin</div>
                </div>
            </div>
            <!-- Authentication -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class='bx bx-log-out' id="log_out"></i>
                </x-responsive-nav-link>
            </form>
        </li>
    </ul>
</div>