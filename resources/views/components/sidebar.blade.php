<header class="main-nav">
    <div class="sidebar-user text-center"><a href="{{ route('profile') }}">
            <img class="img-90 rounded-circle"
                src="{{ Auth::user()->picture ? asset(Auth::user()->picture) : asset('assets/images/lamine.webp') }}"
                alt="Logo" style="width: 90px; height: 90px; object-fit: cover;">
        </a>
        <h6 class="mt-3 f-14 f-w-600">{{ Auth::user()->full_name }}</h6>
        <p class="mb-0 font-roboto">Admin WEA PM Motor</p>
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav">
                <ul class="nav-menu custom-scrollbar">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                                aria-hidden="true"></i></div>
                    </li>
                    <li class="sidebar-main-title">
                        <div>
                            <h6>Main Menu</h6>
                        </div>
                    </li>
                    <li class="dropdown"><a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i
                                data-feather="home"></i><span>Dashboard</span></a>
                    </li>
                    <li class="dropdown"><a href="{{ route('motors.index') }}"
                            class="nav-link {{ request()->routeIs('motors.*') ? 'active' : '' }}"><i
                                data-feather="aperture"></i><span>Motors</span></a>
                    </li>
                    <li class="dropdown"><a href="{{ route('schedules.index') }}"
                            class="nav-link {{ request()->routeIs('schedules.*') ? 'active' : '' }}"><i
                                data-feather="calendar"></i></i><span>Schedules</span></a>
                    </li>
                    <li class="dropdown"><a href="{{ route('maintenance.index') }}"
                            class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}"><i
                                data-feather="activity"></i><span>Maintenance Input</span></a>
                    </li>
                    <li class="dropdown"><a href="{{ route('reports.index') }}"
                            class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"><i
                                data-feather="bar-chart-2"></i><span>Reports</span></a>
                    </li>
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</header>