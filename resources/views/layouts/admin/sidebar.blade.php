<div class="sidebar" data-color="purple" data-image="{{ asset('assets/img/sidebar-5.jpg') }}">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="#" class="simple-text">Fashion Admin</a>
        </div>
        <ul class="nav">
            <li class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="pe-7s-graph"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="{{ request()->is('admin/products*') ? 'active' : '' }}">
                <a href="">
                    <i class="pe-7s-box2"></i>
                    <p>Products</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="pe-7s-cart"></i>
                    <p>Orders</p>
                </a>
            </li>
            <li>
                 <a href="{{ route('admin.accounts.index') }}" class="btn btn-primary">
        Quản lý tài khoản Admin
    </a>
            </li>
        </ul>
    </div>
</div>
