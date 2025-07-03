<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;">
                        <a href="{{ route('admin.panel') }}" class="site_title"><i class="fa fa-book"></i> <span>Book-Store
                                Dashboard</span></a>
                    </div>

                    <div class="clearfix"></div>

                    <!-- menu profile quick info -->
                    <div class="profile clearfix">
                        <div class="profile_pic">
                            @if (Auth::user()->image)
                                <img src="{{ Auth::user()->image() }}" alt="Profile" class="img-circle profile_img">
                            @endif
                        </div>
                        <div class="profile_info">
                            <span>Welcome,</span>
                            <h2>{{ Auth::user()->name }}</h2>
                        </div>
                    </div>
                    <!-- /menu profile quick info -->

                    <br />

                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <h3>General</h3>
                            <ul class="nav side-menu">
                                <li>
                                    <a><i class="fa fa-users"></i> Manage Users <span
                                            class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('admin.panel') }}">All Users</a></li>
                                        <li><a href="{{ route('admin.users.create') }}">Add User</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a><i class="fa fa-list"></i> Categories <span
                                            class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('admin.categories.index') }}">Categories List</a></li>
                                        <li><a href="{{ route('admin.categories.create') }}">Add Category</a></li>
                                    </ul>
                                </li>

                                <li>
                                    <a><i class="fa fa-book"></i> Products <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('admin.products.index') }}">All Products</a></li>
                                        <li><a href="{{ route('admin.products.create') }}">Add Product</a></li>
                                    </ul>
                                </li>

                                <li>
                                    <a><i class="fa fa-tags"></i> Offers <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('admin.discounts.index') }}">All Offers</a></li>
                                        <li><a href="{{ route('admin.discounts.create') }}">Add Offer</a></li>
                                    </ul>
                                </li>
                                 <li>
                                    <a><i class="fa fa-shopping-cart"></i> Manage Orders <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('admin.orders.index') }}">All Orders</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-envelope"></i> Messages <span
                                            class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="messages.html">Inbox</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- /sidebar menu -->

                    <!-- menu footer buttons -->
                    <div class="sidebar-footer hidden-small">
                        <a data-toggle="tooltip" data-placement="top" title="Settings">
                            <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                            <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" title="Lock">
                            <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                        </a>
                        <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                            <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                        </a>
                    </div>
                    <!-- /menu footer buttons -->
                </div>
            </div>
