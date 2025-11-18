<!-- sherah Admin Menu -->
<div class="sherah-smenu">
    <!-- Admin Menu -->
    <div class="admin-menu">

        <!-- Logo -->
        <div class="logo sherah-sidebar-padding">
            <a href="{{ route('admin.dashboard') }}">

                <img class="sherah-logo__main" src="{{ asset('logo-ega-horizontal.svg') }}" alt="EGA Fashion Shop"
                    style="max-height: 50px; width: auto;">
            </a>
            <div class="sherah__sicon close-icon d-xl-none">
                <svg width="9" height="15" viewBox="0 0 9 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6.19855 7.41927C4.22908 5.52503 2.34913 3.72698 0.487273 1.90989C0.274898 1.70227 0.0977597 1.40419 0.026333 1.11848C-0.0746168 0.717537 0.122521 0.36707 0.483464 0.154695C0.856788 -0.0643475 1.24249 -0.0519669 1.60248 0.199455C1.73105 0.289929 1.84438 0.404212 1.95771 0.514685C4.00528 2.48321 6.05189 4.45173 8.09755 6.4212C8.82896 7.12499 8.83372 7.6145 8.11565 8.30687C6.05856 10.2878 4.00052 12.2677 1.94152 14.2467C1.82724 14.3562 1.71391 14.4696 1.58439 14.5591C1.17773 14.841 0.615842 14.781 0.27966 14.4324C-0.056522 14.0829 -0.0946163 13.5191 0.202519 13.1248C0.296802 12.9991 0.415847 12.8915 0.530129 12.781C2.29104 11.0868 4.05194 9.39351 5.81571 7.70212C5.91761 7.60593 6.04332 7.53355 6.19855 7.41927Z">
                    </path>
                </svg>
            </div>
        </div>
        <!-- Main Menu -->
        <div class="admin-menu__one sherah-sidebar-padding">
            <!-- Nav Menu -->
            <div class="menu-bar">
                <ul class="menu-bar__one sherah-dashboard-menu" id="sherahMenu">
                    <li>
                        <a href="{{ route('admin.reports.index') }}">
                            <span class="menu-bar__text">
                                <span class="sherah-menu-icon sherah-svg-icon__v1">
                                    <svg class="sherah-svg-icon" xmlns="http://www.w3.org/2000/svg" width="18.075"
                                        height="18.075" viewBox="0 0 18.075 18.075">
                                        <g id="Icon" transform="translate(0 0)">
                                            <path id="Path_29" data-name="Path 29"
                                                d="M6.966,6.025H1.318A1.319,1.319,0,0,1,0,4.707V1.318A1.319,1.319,0,0,1,1.318,0H6.966A1.319,1.319,0,0,1,8.284,1.318V4.707A1.319,1.319,0,0,1,6.966,6.025ZM1.318,1.13a.188.188,0,0,0-.188.188V4.707a.188.188,0,0,0,.188.188H6.966a.188.188,0,0,0,.188-.188V1.318a.188.188,0,0,0-.188-.188Zm0,0" />
                                            <path id="Path_30" data-name="Path 30"
                                                d="M6.966,223.876H1.318A1.319,1.319,0,0,1,0,222.558V214.65a1.319,1.319,0,0,1,1.318-1.318H6.966a1.319,1.319,0,0,1,1.318,1.318v7.908A1.319,1.319,0,0,1,6.966,223.876Zm-5.648-9.414a.188.188,0,0,0-.188.188v7.908a.188.188,0,0,0,.188.188H6.966a.188.188,0,0,0,.188-.188V214.65a.188.188,0,0,0-.188-.188Zm0,0"
                                                transform="translate(0 -205.801)" />
                                            <path id="Path_31" data-name="Path 31"
                                                d="M284.3,347.357H278.65a1.319,1.319,0,0,1-1.318-1.318V342.65a1.319,1.319,0,0,1,1.318-1.318H284.3a1.319,1.319,0,0,1,1.318,1.318v3.389A1.319,1.319,0,0,1,284.3,347.357Zm-5.648-4.9a.188.188,0,0,0-.188.188v3.389a.188.188,0,0,0,.188.188H284.3a.188.188,0,0,0,.188-.188V342.65a.188.188,0,0,0-.188-.188Zm0,0"
                                                transform="translate(-267.542 -329.282)" />
                                            <path id="Path_32" data-name="Path 32"
                                                d="M284.3,10.544H278.65a1.319,1.319,0,0,1-1.318-1.318V1.318A1.319,1.319,0,0,1,278.65,0H284.3a1.319,1.319,0,0,1,1.318,1.318V9.226A1.319,1.319,0,0,1,284.3,10.544ZM278.65,1.13a.188.188,0,0,0-.188.188V9.226a.188.188,0,0,0,.188.188H284.3a.188.188,0,0,0,.188-.188V1.318a.188.188,0,0,0-.188-.188Zm0,0"
                                                transform="translate(-267.542)" />
                                        </g>
                                    </svg>
                                </span>
                                <span class="menu-bar__name">Dashboard</span>
                            </span>
                        </a>
                    </li>
                    {{-- QUẢN LÝ SẢN PHẨM --}}
                    <li>
                        <a href="#!" class="collapsed" data-bs-toggle="collapse"
                            data-bs-target="#menu-product-management">
                            <span class="menu-bar__text">
                                <span class="sherah-menu-icon sherah-svg-icon__v1">
                                    <svg class="sherah-svg-icon" xmlns="http://www.w3.org/2000/svg" width="20"
                                        height="20" viewBox="0 0 24 24">
                                        <path d="M3 3h18v2H3zm0 6h18v2H3zm0 6h18v2H3z" />
                                    </svg>
                                </span>
                                <span class="menu-bar__name">Quản lý sản phẩm</span>
                            </span>
                            <span class="sherah__toggle"></span>
                        </a>

                        <div class="collapse sherah__dropdown" id="menu-product-management"
                            data-bs-parent="#sherahMenu">
                            <ul class="menu-bar__one-dropdown">

                                <li><a href="{{ route('admin.categories.index') }}"><span class="menu-bar__text"><span
                                                class="menu-bar__name">Danh mục</span></span></a></li>
                                <li><a href="{{ route('admin.products.index') }}"><span class="menu-bar__text"><span
                                                class="menu-bar__name">Sản phẩm</span></span></a></li>
                                <li><a href="{{ route('admin.products.create') }}"><span class="menu-bar__text"><span
                                                class="menu-bar__name">Thêm sản phẩm</span></span></a></li>
                                <li><a href="{{ route('admin.orders.index') }}"><span class="menu-bar__text"><span
                                                class="menu-bar__name">Đơn hàng</span></span></a></li>
                                <li><a href="{{ route('admin.returns.index') }}"><span class="menu-bar__text"><span
                                                class="menu-bar__name">Hoàn hàng</span></span></a></li>
                                <li><a href="{{ route('admin.vouchers.index') }}"><span class="menu-bar__text"><span
                                                class="menu-bar__name">Vouchers</span></span></a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#!" class="collapsed" data-bs-toggle="collapse"
                            data-bs-target="#menu-account-management">
                            <span class="menu-bar__text">
                                <span class="sherah-menu-icon sherah-svg-icon__v1">
                                    <svg class="sherah-svg-icon" xmlns="http://www.w3.org/2000/svg" width="20"
                                        height="20" viewBox="0 0 24 24">
                                        <path
                                            d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z" />

                                    </svg>
                                </span>
                                <span class="menu-bar__name">Quản lý tài khoản</span>
                            </span>
                            <span class="sherah__toggle"></span>
                        </a>

                        <div class="collapse sherah__dropdown" id="menu-account-management"
                            data-bs-parent="#sherahMenu">
                            <ul class="menu-bar__one-dropdown">
                                <li><a href="{{ route('admin.customers.index') }}"><span class="menu-bar__text"><span
                                                class="menu-bar__name">Tài khoản khách hàng</span></span></a></li>
                                <li><a href="{{ route('admin.accounts.index') }}"><span class="menu-bar__text"><span
                                                class="menu-bar__name">Tài khoản Admin</span></span></a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#!" class="collapsed" data-bs-toggle="collapse"
                            data-bs-target="#menu-item_payment_methods">
                            <span class="menu-bar__text">
                                <span class="sherah-menu-icon sherah-svg-icon__v1">
                                    <!-- Premium Credit Card Icon -->
                                    <svg class="sherah-svg-icon" xmlns="http://www.w3.org/2000/svg" width="20"
                                        height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <!-- Card body with rounded corners -->
                                        <rect x="2" y="5" width="20" height="14" rx="2.5"
                                            ry="2.5" fill="none" stroke="currentColor" />
                                        <!-- Chip -->
                                        <rect x="4" y="9" width="4" height="5" rx="0.8"
                                            fill="none" stroke="currentColor" stroke-width="1.5" />
                                        <!-- Card number lines -->
                                        <line x1="10" y1="11" x2="18" y2="11"
                                            stroke="currentColor" stroke-width="1.5" />
                                        <line x1="10" y1="13.5" x2="16" y2="13.5"
                                            stroke="currentColor" stroke-width="1.5" />
                                        <!-- Expiry -->
                                        <line x1="10" y1="16" x2="14" y2="16"
                                            stroke="currentColor" stroke-width="1.5" />
                                        <!-- Premium badge -->
                                        <circle cx="19" cy="8" r="2" fill="currentColor"
                                            opacity="0.3" />
                                    </svg>
                                </span>
                                <span class="menu-bar__name">Phương thức thanh toán</span>
                            </span>
                            <span class="sherah__toggle"></span>
                        </a>
                        <div class="collapse sherah__dropdown" id="menu-item_payment_methods"
                            data-bs-parent="#sherahMenu">
                            <ul class="menu-bar__one-dropdown">
                                <li><a href="{{ route('admin.payment-methods.index') }}"><span
                                            class="menu-bar__text"><span class="menu-bar__name">Danh sách phương
                                                thức</span></span></a></li>
                                <li><a href="{{ route('admin.payment-methods.create') }}"><span
                                            class="menu-bar__text"><span class="menu-bar__name">Thêm phương thức
                                                mới</span></span></a></li>
                            </ul>
                        </div>
                    </li>
                    {{-- QUẢN LÝ NỘI DUNG --}}
                    <li>
                        <a href="#!" class="collapsed" data-bs-toggle="collapse"
                            data-bs-target="#menu-content-management">
                            <span class="menu-bar__text">

                                <span class="sherah-menu-icon sherah-svg-icon__v1">
                                    <svg class="sherah-svg-icon" xmlns="http://www.w3.org/2000/svg" width="18"
                                        height="18" viewBox="0 0 16 16" aria-hidden="true">
                                        <!-- tờ giấy -->
                                        <rect x="2.5" y="3.5" width="9" height="11" rx="1.5"
                                            fill="#fff" stroke="#000" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <!-- tờ chồng phía sau -->
                                        <rect x="4.5" y="1.5" width="9" height="11" rx="1.5"
                                            fill="#fff" stroke="#000" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <!-- các dòng nội dung -->
                                        <line x1="6" y1="5.5" x2="11.5" y2="5.5"
                                            stroke="#000" stroke-width="1.5" stroke-linecap="round" />
                                        <line x1="6" y1="8" x2="11.5" y2="8"
                                            stroke="#000" stroke-width="1.5" stroke-linecap="round" />
                                        <line x1="6" y1="10.5" x2="9.5" y2="10.5"
                                            stroke="#000" stroke-width="1.5" stroke-linecap="round" />
                                    </svg>
                                </span>
                                <span class="menu-bar__name">Quản lý nội dung</span>
                            </span>
                            <span class="sherah__toggle"></span>
                        </a>

                        <div class="collapse sherah__dropdown" id="menu-content-management"
                            data-bs-parent="#sherahMenu">
                            <ul class="menu-bar__one-dropdown">

                                {{-- Banner --}}
                                <li>
                                    <a href="{{ route('admin.banners.index') }}">
                                        <span class="menu-bar__text">
                                            <span class="menu-bar__name">Banner</span>
                                        </span>
                                    </a>
                                </li>

                                {{-- Post --}}
                                <li>
                                    <a href="{{ route('admin.posts.index') }}">
                                        <span class="menu-bar__text">
                                            <span class="menu-bar__name">Post</span>
                                        </span>
                                    </a>
                                </li>

                                {{-- Cài đặt shop --}}
                                <li>
                                    <a href="{{ route('admin.shop-settings.edit') }}">
                                        <span class="menu-bar__text">
                                            <span class="menu-bar__name">Cài đặt shop</span>
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.contacts.index') }}">
                                        <span class="menu-bar__text">
                                            <span class="menu-bar__name">Liên hệ & Hỗ trợ</span>
                                        </span>
                                    </a>
                                </li>


                            </ul>
                        </div>
                    </li>
                    {{-- Login --}}
                    {{-- <li><a class="collapsed" href="{{ }}"><span class="menu-bar__text">
                                <span class="sherah-menu-icon sherah-svg-icon__v1">
                                    <svg class="sherah-svg-icon" xmlns="http://www.w3.org/2000/svg" width="19.103"
                                        height="23.047" viewBox="0 0 19.103 23.047">
                                        <g id="Icon" transform="translate(-209.904 -251.466)">
                                            <path id="Path_240" data-name="Path 240"
                                                d="M212.282,260.761c0-.958-.016-1.929,0-2.9a6.662,6.662,0,0,1,5.78-6.272c4.429-.777,8.475,2.182,8.562,6.273.021.97,0,1.94,0,2.925.264.049.49.077.708.134a2.1,2.1,0,0,1,1.656,1.995c.024,1.769.01,3.539.012,5.308,0,1.323.007,2.646,0,3.969-.009,1.47-.933,2.311-2.567,2.314q-6.98.011-13.96,0c-1.657,0-2.566-.847-2.568-2.362q-.007-4.448,0-8.9c0-1.438.616-2.115,2.185-2.421A1.584,1.584,0,0,0,212.282,260.761Zm7.156,12.3q3.436,0,6.871,0c.925,0,1.1-.163,1.1-1.014q0-4.4,0-8.8c0-.8-.2-.983-1.09-.984q-6.871,0-13.742,0c-.867,0-1.072.185-1.073.95q0,4.445,0,8.891c0,.776.2.95,1.063.951Q216,273.064,219.437,273.061Zm-5.62-12.274h11.215c0-1.014.034-2-.007-2.98a5.223,5.223,0,0,0-4.93-4.866c-2.992-.229-5.547,1.367-6.063,3.958A26.567,26.567,0,0,0,213.817,260.787Z"
                                                transform="translate(0)" />
                                            <path id="Path_241" data-name="Path 241"
                                                d="M279.688,386.981a2.131,2.131,0,0,1,2.059,1.549,2.1,2.1,0,0,1-1.038,2.476.523.523,0,0,0-.32.557c.013.4.017.8-.008,1.193a.715.715,0,0,1-1.429.007c-.01-.143-.011-.286-.008-.429.015-.641.059-1.2-.691-1.617a1.921,1.921,0,0,1-.6-2.359A2.113,2.113,0,0,1,279.688,386.981Zm.689,2.152a.709.709,0,1,0-1.417.041.658.658,0,0,0,.7.678A.666.666,0,0,0,280.376,389.133Z"
                                                transform="translate(-60.212 -122.554)" />
                                            <path id="Path_242" data-name="Path 242"
                                                d="M294.225,402.762a.666.666,0,0,1-.713.719.658.658,0,0,1-.7-.678.709.709,0,1,1,1.417-.041Z"
                                                transform="translate(-74.06 -136.182)" />
                                        </g>
                                    </svg>
                                </span>
                                <span class="menu-bar__name">Website</span></span></a></span>
                    </li> --}}
                </ul>
            </div>
            <!-- End Nav Menu -->
        </div>

    </div>
    <!-- End Admin Menu -->
</div>
<!-- End sherah Admin Menu -->
