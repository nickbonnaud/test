<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pockeyt Business</title>
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/libs.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/icon" />
</head>

<body>
<nav class="navbar-post navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbarPost-header">
            <a class="navbarPost-brand" href="http://www.pockeyt.com/" target="_blank">
                <img src="{{ asset('/images/logo-horizontal-white.png') }}" class="logoPost">
            </a>
        </div>
    </div>
</nav>

<div class="wrapper">
    <div class="container">
        @yield('content')
    </div>
</div>

<footer class="postFooter">
    <p>Want to find more deals, specials, and events just like this?</p>
    <div class="callAction">
    	<a href="https://bnc.lt/igem/py4BFjI1ou" target="_blank"><p>Download Pockeyt Now - Free.</p></a>
    </div>
    <a href="https://bnc.lt/igem/py4BFjI1ou" target="_blank"><img src="{{ asset('/images/googlePlayBadge.png') }}" class="storeBadge"></a>
    <a href="https://bnc.lt/igem/py4BFjI1ou" target="_blank"><img src="{{ asset('/images/iosStoreBadge.png') }}" class="storeBadge"></a>
</footer>

<script src="{{ asset('/js/libs.js') }}"></script>
@yield('scripts.footer')
</body>
</html>