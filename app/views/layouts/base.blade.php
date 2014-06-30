<!DOCTYPE html>
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title>{{{ $settings->site_name }}}</title>
    @yield('metas')

    <link rel="stylesheet" type="text/css" href="/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/components/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/components/jquery-ui/themes/base/jquery.ui.all.css">
    <link rel="stylesheet" type="text/css" href="/css/theme.css">
    <link rel="stylesheet" type="text/css" href="/css/primitives.css">
    <link rel="stylesheet" type="text/css" href="/css/navbar.css">
    <link rel="stylesheet" type="text/css" href="/css/flash.css">
    <link rel="stylesheet" type="text/css" href="/css/devise.css">
    <link rel="stylesheet" type="text/css" href="/css/admin.css">
    <link rel="stylesheet" type="text/css" href="/css/campaigns.css">
    <link rel="stylesheet" type="text/css" href="/css/users.css">

    @yield('stylesheets')

    <script src="/components/jquery/jquery.js"></script>
    <script src="/components/bootstrap/js/bootstrap.js"></script>
    <script src="/components/jquery-ui/ui/jquery-ui.js"></script>
    <script src="/components/moment/moment-built.js"></script>    
    <script src="/components/html5shiv/dist/html5shiv.js"></script>
    <script src="/js/main.js"></script>
    <script src="/js/admin.js"></script>
    <script src="/js/campaigns.js"></script>

    @yield('scripts')
  </head>

  <!--[if lt IE 9 ]><body class="lt-ie9"><![endif]-->
  <!--[if (gt IE 9)|!(IE)]><!--> <body><!--<![endif]-->

    @include( 'navbar' )

    @yield( 'header' )

    @if (Session::has('message'))
      <div class="alert alert-info">{{ Session::get('message') }}</div>
    @elseif (Session::has('errors'))
      <div class="alert alert-alert">{{ Session::get('errors')->first() }}</div>
    @endif

    <div id="main">
      @yield( 'main' )
    </div>

    @include( 'footer' )

    <div id="powered">
      <a href="http://www.mobishift.com">&nbsp;&nbsp;A Cashive Open Site</a>
      &nbsp;&nbsp;<img src='/images/crowdtiltcircle.png' />&nbsp;&nbsp;
      <a href="http://www.mobishift.com">Powered by Mobishift</a>
    </div>

    @yield('body_bottom')

  </body>

</html>
