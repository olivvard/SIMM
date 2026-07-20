<div class="page-main-header">
    <div class="main-header-right row m-0">

        {{-- ── Logo + Desktop Sidebar Toggle ──────────────────────────── --}}
        <div class="main-header-left">
            {{-- Logo: diberi fixed width agar tidak gepeng di mobile --}}
            <div class="logo-wrapper">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('wea-new.png') }}" alt="WEA"
                         style="height: 40px; width: auto; max-width: 140px; object-fit: contain;">
                </a>
            </div>
            <div class="dark-logo-wrapper">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('wea-new.png') }}" alt="WEA"
                         style="height: 40px; width: auto; max-width: 140px; object-fit: contain;">
                </a>
            </div>

            {{-- Desktop sidebar toggle (hamburger) --}}
            <div class="toggle-sidebar" id="sidebar-toggle-desktop">
                <i class="status_toggle middle" data-feather="align-center" id="sidebar-toggle"></i>
            </div>
        </div>

        {{-- ── Search bar (center, desktop only) ──────────────────────── --}}
        <div class="left-menu-header col">
            <ul>
                <li>
                    <form class="form-inline search-form">
                        <div class="search-bg">
                            <i class="fa fa-search"></i>
                            <input class="form-control-plaintext" placeholder="Search here.....">
                        </div>
                    </form>
                    <span class="d-sm-none mobile-search search-bg">
                        <i class="fa fa-search"></i>
                    </span>
                </li>
            </ul>
        </div>

        {{-- ── Right: Dark mode + Logout ───────────────────────────────── --}}
        <div class="nav-right col pull-right right-menu p-0 box-col-6">
            <ul class="nav-menus">
                <li>
                    <div class="mode pointer"><i class="fa fa-moon-o"></i></div>
                </li>
                <li class="onhover-dropdown p-0">
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button class="btn btn-primary-light" type="submit">
                            <i data-feather="log-out"></i>Log out
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        {{-- ── Mobile burger (visible hanya < lg) ─────────────────────── --}}
        <div class="d-lg-none mobile-toggle pull-right w-auto" id="mobile-sidebar-toggle">
            <i data-feather="menu"></i>
        </div>

    </div>
</div>