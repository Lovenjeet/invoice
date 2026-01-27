<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h3 class="sidebar-brand">
            <i class="bi bi-shield-check text-primary"></i>
            <span class="text-primary">{{ config('app.name') }}</span>
        </h3>
        <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i>
                    <span>Suppliers</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('bill-tos.index') }}" class="nav-link {{ request()->routeIs('bill-tos.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Bill To</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('ship-tos.index') }}" class="nav-link {{ request()->routeIs('ship-tos.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Ship To</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Invoices</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('hs-codes.index') }}" class="nav-link {{ request()->routeIs('hs-codes.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span>SKUs</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<div class="sidebar-overlay d-lg-none" id="sidebarOverlay"></div>

