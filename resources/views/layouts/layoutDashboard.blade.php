<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <script src="{{ asset('/vendor/standAlone/stand-alone.js') }}"></script>
  <meta name="apple-mobile-web-app-title" content="Pockeyt Business">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta property="og:site_name" content="Pockeyt" />
  <title>Pockeyt Business</title>
  <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/AdminLTE.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/skin-yellow.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/sweetalert/dist/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/select2/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/pace/pace-theme-minimal.css') }}">
  <link rel="manifest" href="/manifest.json">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.css">
  <link rel="shortcut icon" href="/images/favicon.ico" type="image/icon" />
  <link rel="apple-touch-startup-image" href="/images/launch.png">
</head>

<body class="hold-transition skin-yellow sidebar-mini">
  <div class="wrapper" id="app">

    <header class="main-header">
      <!-- Logo -->
      <a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">
          <img src="{{ asset('/images/white-logo.png') }}">
        </span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
          <img src="{{ asset('/images/logo-horizontal-white.png') }}">
        </span>
      </a>
      <!-- Header Navbar: style can be found in header.less -->
      <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  @if(is_null($user->photo))
                      <img src="{{ asset('/images/icon-profile-photo.png') }}" class="user-image" alt="User Image">
                  @else
                      <img src="{{ $user->photo->url }}" class="user-image" alt="User Image">
                  @endif
                <span class="hidden-xs">{{ $user->first_name }}</span>
              </a>
              <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header">
                  @if(is_null($user->photo))
                      <img src="{{ asset('/images/icon-profile-photo.png') }}" class="img-circle" alt="User Image">
                  @else
                      <img src="{{ $user->photo->url }}" class="img-circle" alt="User Image">
                  @endif
                  <p>
                    {{ $user->first_name }} {{ $user->last_name }}
                  </p>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="{{ route('users.show', ['users' => $user->id])  }}" class="btn btn-default btn-flat">User Profile</a>
                  </div>
                  <div class="pull-right">
                    <a href="{{ route('logout') }}"
                      onclick="event.preventDefault();
                      document.getElementById('logout-form').submit();" class="btn btn-default btn-flat">Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                    </form>
                  </div>
                </li>
              </ul>
            </li>
            <!-- Control Sidebar Toggle Button -->
            <li>
              <a href="#" data-toggle="control-sidebar"><i class="fa fa-check-square-o"></i></a>
            </li>
          </ul>
        </div>
      </nav>
    </header>

    <!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
          <div class="pull-left image">
              @if(is_null($profile->logo))
                  <img src="{{ asset('/images/icon-profile-photo.png') }}" class="img-circle" alt="Profile Image">
              @else
                  <img src="{{ $profile->logo->url }}" class="img-circle" alt="Profile Image">
              @endif
          </div>
          <div class="pull-left info profile-status">
              <p>{{ $profile->business_name }}</p>
              @if($profile->approved)
                  <span><i class="fa fa-circle text-success"></i> Profile Approved</span>
              @else
                  <span href="#"><i class="fa fa-circle text-danger"></i> Profile Waiting Approval</span>
              @endif
          </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
          <li class="header">MAIN NAVIGATION</li>
          <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> <span class="menu-text">Dashboard</span></a></li>
          <li class="treeview">
            <a href="#">
              <i class="fa fa-building"></i>
              <span class="menu-text">Your Business Info</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ route('profiles.edit', ['profiles' => $profile->slug])  }}"><i class="fa fa-circle-o"></i> Profile Info</a></li>
              @if(!$profile->account)
                <li><a href="{{ route('accounts.create', ['profiles' => $profile->slug]) }}"><i class="fa fa-circle-o"></i> Create Payment Account</a></li>
              @else
                <li><a href="{{ $profile->account->route() }}"><i class="fa fa-circle-o"></i> Payment Account Info</a></li>
              @endif
                <li><a href="{{ route('connections.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-circle-o"></i> Account Connections</a></li>
            </ul>
          </li>
          <li><a href="{{ route('posts.profile', ['profiles' => $profile->slug]) }}"><i class="fa fa-rss"></i> <span class="menu-text">Posts</span></a></li>
          <li><a href="{{ route('events.profile', ['profiles' => $profile->slug]) }}"><i class="fa fa-calendar"></i> <span class="menu-text">Events</span></a></li>
          <li><a href="{{ route('products.profile', ['profiles' => $profile->slug]) }}"><i class="fa fa-shopping-cart"></i> <span class="menu-text">Inventory</span></a></li>
          @if($profile->loyaltyProgram)
            <li><a href="{{ route('loyaltyProgram.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-trophy"></i> <span class="menu-text">Loyalty Program</span></a></li>
          @else
            <li><a href="{{ route('loyaltyProgram.create', ['profiles' => $profile->slug]) }}"><i class="fa fa-trophy"></i> <span class="menu-text">Loyalty Program</span></a></li>
          @endif
          <li><a href="{{ route('deals.profile', ['profiles' => $profile->slug]) }}"><i class="fa fa-bolt"></i> <span class="menu-text">Deals</span></a></li>
          <li><a href="{{ route('postAnalytics.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-line-chart"></i> <span class="menu-text">Analytics Dashboard</span></a></li>

          <li class="treeview">
            <a href="#">
              <i class="fa fa-archive"></i>
              <span class="menu-text">Sales Center</span>
              <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
            </a>
            <ul class="treeview-menu">
              <li><a href="{{ route('salesHistory.show', ['profiles' => $profile->slug]) }}?defaultDate=1"><i class="fa fa-circle-o"></i> Sales Breakdown</a></li>
              @if($profile->tip_tracking_enabled)
                <li><a href="{{ route('team.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-circle-o"></i> Team</a></li>
              @endif
              <li><a href="{{ route('refunds.index', ['profile' => $profile->slug]) }}"><i class="fa fa-circle-o"></i> Issue Refund</a></li>
            </ul>
          </li>
          @if($user->is_admin)
            <li><a href="{{ route('review.business') }}"><i class="fa fa-key"></i> <span class="menu-text">Pending Businesses</span></a></li>
          @endif
        </ul>
      </section>
      <!-- /.sidebar -->
    </aside>

@yield('content')
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Create the tabs -->
      <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-spinner"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-check"></i></a></li>
      </ul>
      <!-- Tab panes -->
      <div class="tab-content" id="tab">
        <!-- Home tab content -->
        <sidebar-pending profile-slug="{{ $profile->slug }}"></sidebar-pending>
        <!-- /.tab-pane -->
        <!-- Stats tab content -->
        <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
        <!-- /.tab-pane -->
        <!-- Settings tab content -->
        <sidebar-finalized profile-slug="{{ $profile->slug }}"></sidebar-finalized>
        <!-- /.tab-pane -->
      </div>
    </aside>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>

    <customer-reward profile-slug="{{ $profile->slug }}"></customer-reward>
    <transaction-error profile-slug="{{ $profile->slug }}"></transaction-error>
    <transactions-change profile-slug="{{ $profile->slug }}"></transactions-change>
    <transaction-success profile-slug="{{ $profile->slug }}"></transaction-success>
    <customer-request-bill profile-slug="{{ $profile->slug }}"></customer-request-bill>

  </div>
    <!-- ./wrapper -->
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="{{ asset('/vendor/pace/pace.min.js') }}"></script>
	<script src="{{ asset('/vendor/slimScroll/jquery.slimscroll.min.js') }}"></script>
	<script src="{{ asset('/vendor/fastclick/fastclick.js') }}"></script>
	<script src="{{ asset('/js/app.min.js') }}"></script>
  <script src="{{ asset('/vendor/sweetalert/dist/sweetalert.min.js') }}"></script>
  <script src="{{ asset('/vendor/toastr/toastr.min.js') }}"></script>
  <script src="{{ asset('/vendor/chart.js/dist/Chart.min.js') }}"></script>
	<script src="{{ asset('/vendor/select2/select2.min.js') }}"></script>
  <script src="{{ asset('/vendor/noBounce/inobounce.min.js') }}"></script>
  <script src="//js.pusher.com/3.2/pusher.min.js"></script>
	@yield('scripts.footer')
  @include('flash.flash')
  <style>
    html { display:none; }
  </style>
  <script>

    if (self == top) { 
      document.documentElement.style.display = 'block'; 
    } else {
      top.location = self.location;
    };

    // tab.loadTransactions();
  </script>
</body>
</html>