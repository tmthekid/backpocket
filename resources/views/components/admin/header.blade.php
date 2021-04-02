<!-- START HEADER -->
<div class="header ">
    <!-- START MOBILE SIDEBAR TOGGLE -->
    <a href="#" class="btn-link toggle-sidebar d-lg-none pg pg-menu" data-toggle="sidebar">
    </a>
    <!-- END MOBILE SIDEBAR TOGGLE -->
    <div class="">
        <div class="brand inline   ">
            <!-- <img src="assets/img/logo.png" alt="logo" data-src="assets/img/logo.png"
    data-src-retina="assets/img/logo_2x.png" width="78" height="22"> -->
            <strong><img width="55%" style="margin-left: 4rem" src="/admin/logo.jpeg" alt="Logo"></strong>
        </div>

    </div>
    <div class="d-flex align-items-center">
        <!-- START User Info-->
        <div class="pull-left p-r-10 fs-14 font-heading d-lg-block d-none">
            <span class="semi-bold">{{ Auth::user()->name }}</span>
        </div>
        <div class="dropdown pull-right d-lg-block d-none">
            <button class="profile-dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                        <span class="thumbnail-wrapper d32 circular inline">
                            <img src="{{ asset('admin/assets/img/profiles/avatar.jpg') }}" alt="" data-src="{{ asset('admin/assets/img/profiles/avatar.jpg') }}"
                                 data-src-retina="{{ asset('admin/assets/img/profiles/avatar_small2x.jpg') }}" width="32" height="32">
                        </span>
            </button>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown" role="menu">
                <a href="#" class="dropdown-item"><i class="pg-settings_small"></i> Account
                    Settings</a>
                <a href="#" class="dropdown-item"><i class="pg-outdent"></i> Feedback</a>
                <a href="#" class="dropdown-item"><i class="pg-signals"></i> Help</a>
                <a class="clearfix bg-master-lighter dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    <span class="pull-left">{{ __('Logout') }}</span>
                    <span class="pull-right"><i class="pg-power"></i></span>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
        <!-- END User Info-->
        <!-- <a href="#" class="header-icon pg pg-alt_menu btn-link m-l-10 sm-no-margin d-inline-block"
  data-toggle="quickview" data-toggle-element="#quickview"></a> -->
    </div>
</div>
<!-- END HEADER -->
