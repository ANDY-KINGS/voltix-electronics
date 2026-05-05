<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Voltix Electronix') }} — @yield('title', 'Dashboard')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #1F3A6E;
            --accent-color: #27AE60;
            --sidebar-color: #1a2942;
        }
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; }
        .sidebar { height: 100vh; background-color: var(--sidebar-color); color: white; position: fixed; width: 250px; z-index: 1000; overflow-y: auto;}
        .sidebar .brand { padding: 20px; font-size: 1.3rem; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center; }
        .sidebar .brand small { font-size: 0.7rem; color: #94a3b8; display: block; margin-top: 2px; font-weight: normal; }
        .sidebar-menu { list-style: none; padding: 0; margin-top: 20px; }
        .sidebar-menu li a { display: block; padding: 12px 20px; color: #cbd5e1; text-decoration: none; transition: 0.3s; font-size: 0.9rem; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background-color: rgba(255,255,255,0.1); color: white; border-left: 4px solid var(--accent-color); }
        .sidebar-menu li a i, .sidebar-menu li a .bi { width: 25px; }
        .sidebar-section { padding: 8px 20px 4px; font-size: 0.68rem; text-transform: uppercase; color: #64748b; letter-spacing: 0.1em; margin-top: 10px; }
        
        .main-content { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { background: white; padding: 15px 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .content-area { padding: 20px; flex: 1; }
        
        .badge-low-stock { background-color: #E74C3C; font-size: 0.75rem; padding: 3px 6px; border-radius: 10px; margin-left: 10px; }
        
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: #152A50; border-color: #152A50; }
        .btn-success { background-color: var(--accent-color); border-color: var(--accent-color); }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-250px); transition: 0.3s; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Pusher & Laravel Echo -->
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = false;
        var pusher = new Pusher('{{ config('mpesa.pusher_key') ?? '411dcaa13214fe1a36c4' }}', {
            cluster: '{{ config('mpesa.pusher_cluster') ?? 'ap2' }}'
        });
    </script>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="brand">
            <i class="fas fa-bolt text-warning"></i> Voltix Electronix
            <small>Powering Your Digital Life</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            
            <li class="sidebar-section">Sales</li>

            @can('process-sales')
                <li><a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}"><i class="fas fa-calculator"></i> POS Terminal</a></li>
            @endcan
            
            @can('manage-orders')
                <li><a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}"><i class="fas fa-file-invoice-dollar"></i> Orders</a></li>
            @endcan

            @can('manage-customers')
                <li><a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}"><i class="fas fa-users"></i> Customers</a></li>
            @endcan

            {{-- Warranty Claims (admin + cashier) --}}
            @auth
            <li>
                <a href="{{ route('warranty-claims.index') }}" class="{{ request()->routeIs('warranty-claims.*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i> Warranty Claims
                    @if(isset($openWarrantyClaims) && $openWarrantyClaims > 0)
                        <span class="badge bg-danger ms-1" style="font-size:0.7rem;">{{ $openWarrantyClaims }}</span>
                    @endif
                </a>
            </li>
            @endauth

            <li class="sidebar-section">Inventory</li>

            @can('manage-products')
                <li>
                    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i> Products
                        @if(isset($lowStockCount) && $lowStockCount > 0)
                            <span class="badge badge-low-stock">{{ $lowStockCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.brands.index') }}" class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i> Brands
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.serial-numbers.index') }}" class="{{ request()->routeIs('admin.serial-numbers.*') ? 'active' : '' }}">
                        <i class="fas fa-barcode"></i> Serial Numbers
                    </a>
                </li>
            @endcan

            @can('manage-categories')
                <li><a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="fas fa-tag"></i> Categories</a></li>
            @endcan

            @can('manage-suppliers')
                <li><a href="{{ route('admin.suppliers.index') }}" class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}"><i class="fas fa-truck"></i> Suppliers</a></li>
            @endcan

            <li class="sidebar-section">Administration</li>

            @can('manage-users')
                <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="fas fa-user-shield"></i> Users</a></li>
            @endcan

            @can('view-reports')
                <li><a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.index') ? 'active' : '' }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
                <li><a href="{{ route('reports.warranty') }}" class="{{ request()->routeIs('reports.warranty') ? 'active' : '' }}"><i class="fas fa-shield-alt"></i> Warranty Report</a></li>
            @endcan
            
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </form>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div>
                <button class="btn btn-outline-secondary d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="fas fa-bars"></i></button>
                <span class="d-none d-md-inline text-muted" style="font-size: 0.85rem;">Voltix Electronix POS</span>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i> {{ Auth::user()->name ?? 'User' }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <!-- Dynamic Content -->
        <main class="content-area">
            @include('components.flash-message')
            
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
