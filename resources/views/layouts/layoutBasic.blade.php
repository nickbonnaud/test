<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="{{ asset('/vendor/standAlone/stand-alone.js') }}"></script>
    <meta name="apple-mobile-web-app-title" content="Pockeyt Business">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Pockeyt Business</title>
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/libs.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/jqueryui/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/pace/pace-theme-minimal.css') }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.css">
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/icon" />
    <link rel="apple-touch-startup-image" href="/images/launch.png">
</head>

<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">
                <img src="{{ asset('/images/pockeyt-logo.png') }}" class="logo">
            </a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                @auth
                    <li><a href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">Logout
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                @else 
                    <li><a href="{{ route('password.email') }}">Forgot Password?</a></li>
                @endauth
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="wrapper">
    <div class="container">
        @yield('content')
    </div>
</div>

<footer>
    <p>Made in Raleigh, NC</p>
    <p>Mentorship from endUp</p>
</footer>
<script src="{{ asset('/vendor/pace/pace.min.js') }}"></script>
<script src="{{ asset('/vendor/jquery/jquery-1.12.0.min.js') }}"></script>
<script src="{{ asset('/vendor/bootstrap/js/bootstrap.js') }}"></script>
<script src="{{ asset('/js/libs.js') }}"></script>
<script src="{{ asset('/vendor/jqueryui/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('/vendor/select2/select2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script src="{{ asset('/vendor/vMask/v-mask.min.js') }}"></script>
<script src="{{ asset('/vendor/vMask/v-money.js') }}"></script>
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
    }


</script>
</body>
</html>