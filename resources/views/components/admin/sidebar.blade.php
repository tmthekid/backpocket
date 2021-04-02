<!-- BEGIN SIDEBPANEL-->
<nav class="page-sidebar" data-pages="sidebar">
    <!-- BEGIN SIDEBAR MENU TOP TRAY CONTENT-->
    <!-- <div class="sidebar-overlay-slide from-top" id="appMenu">
  <div class="row">
    <div class="col-xs-6 no-padding">
      <a href="#" class="p-l-40"><img src="assets/img/demo/social_app.svg" alt="socail">
      </a>
    </div>
    <div class="col-xs-6 no-padding">
      <a href="#" class="p-l-10"><img src="assets/img/demo/email_app.svg" alt="socail">
      </a>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-6 m-t-20 no-padding">
      <a href="#" class="p-l-40"><img src="assets/img/demo/calendar_app.svg" alt="socail">
      </a>
    </div>
    <div class="col-xs-6 m-t-20 no-padding">
      <a href="#" class="p-l-10"><img src="assets/img/demo/add_more.svg" alt="socail">
      </a>
    </div>
  </div>
</div> -->
    <!-- END SIDEBAR MENU TOP TRAY CONTENT-->
    <!-- BEGIN SIDEBAR MENU HEADER-->
    <div class="sidebar-header">
        <!-- <img src="assets/img/logo_white.png" alt="logo" class="brand" data-src="assets/img/logo_white.png"
    data-src-retina="assets/img/logo_white_2x.png" width="78" height="22"> -->
        <img width="25%" style="background: #fff; padding: 10px;" src="/admin/logo-nav.jpeg" alt="Logo">
        <div class="sidebar-header-controls">
            <!-- <button type="button" class="btn btn-xs sidebar-slide-toggle btn-link m-l-20" data-pages-toggle="#appMenu"><i
        class="fa fa-angle-down fs-16"></i>
    </button> -->
            <button type="button"
                    class="btn btn-link d-lg-inline-block d-xlg-inline-block d-md-inline-block d-sm-none d-none"
                    data-toggle-pin="sidebar"><i class="fa fs-12"></i>
            </button>
        </div>
    </div>
    <!-- END SIDEBAR MENU HEADER-->
    <!-- START SIDEBAR MENU -->
    <div class="sidebar-menu">
        <!-- BEGIN SIDEBAR MENU ITEMS-->
        <ul class="menu-items">
            <li class="m-t-30 ">
                <a href="{{ route('admin.dashboard') }}" class="detailed">
                    <span class="title">Dashboard</span>
                </a>
                <span class="{{ (request()->is('admin/dashboard')) ? 'bg-success' : '' }} icon-thumbnail"><i class="pg-home"></i></span>
            </li>
            <li>
                <a href="/" class="detailed">
                    <span class="">Inbox</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-inbox"></i></span>
            </li>
            <li>
                <a href="{{ route('vendors.list') }}" class="detailed">
                    <span class="titls">Vendors</span>
                </a>
                <span class="{{ (request()->is('admin/vendors*')) ? 'bg-success' : '' }} icon-thumbnail"><i class="fa fa-users"></i></span>
            </li>
            <li>
                <a href="{{ route('products.list') }}" class="detailed">
                    <span class="titls">Products</span>
                </a>
                <span class="{{ (request()->is('admin/products*')) ? 'bg-success' : '' }} icon-thumbnail"><i class="fa fa-product-hunt"></i></span>
            </li>
            <li>
                <a href="{{ route('transactions.list') }}" class="detailed">
                    <span class="title">Transactions</span>
                </a>
                <span class="{{ (request()->is('admin/transactions*')) ? 'bg-success' : '' }} icon-thumbnail"><i class="pg-charts"></i></span>
            </li>
            <li>
                <a href="/" class="detailed">
                    <span class="">Users</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-users"></i></span>
            </li>
            <li>
                <a href="/" class="detailed">
                    <span class="">Reports</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-file"></i></span>
            </li>
            <li>
                <a href="/" class="detailed">
                    <span class="">Settings</span>
                </a>
                <span class="icon-thumbnail"><i class="fa fa-wrench"></i></span>
            </li>
        </ul>

        <div class="clearfix"></div>
    </div>
    <!-- END SIDEBAR MENU -->
</nav>
<!-- END SIDEBAR -->
<!-- END SIDEBPANEL-->
