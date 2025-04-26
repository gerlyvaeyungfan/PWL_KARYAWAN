<div class="sidebar">
    <!-- SidebarSearch Form -->
    <div class="form-inline mt-2">
        <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-sidebar">
                    <i class="fas fa-search fa-fw"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
                <a href="{{ url('/') }}" class="nav-link {{ ($activeMenu == 'dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <li class="nav-header">Data Perusahaan</li>
            <li class="nav-item">
                <a href="{{ url('/jabatan') }}" class="nav-link {{ ($activeMenu == 'jabatan') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-briefcase"></i>
                    <p>Data Jabatan</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('/karyawan') }}" class="nav-link {{ ($activeMenu == 'karyawan') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Data Karyawan</p>
                </a>
            </li>

            <li class="nav-header">Data Transaksi</li>
            <li class="nav-item">
                <a href="{{ url('/gaji') }}" class="nav-link {{ ($activeMenu == 'gaji') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-money-bill-wave"></i>
                    <p>Data Gaji Karyawan</p>
                </a>
            </li>
        </ul>
    </nav>
</div>
