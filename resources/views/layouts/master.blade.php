@inject('service', 'App\helpers\BladeServices');

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="robots" content="noindex, nofollow">
        <meta name="googlebot" content="noindex">
        <title>File Manger</title>
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.0.3/styles/vs.min.css">
        <link rel="stylesheet" href="{{ asset('/css/file-manager.css')}}">

        @yield('extra_heads')
    </head>
    <body class="navbar-normal">
        @include('files.includes.navigation')

        <div id="wrapper" class="container-fluid">
            <!-- main file lists -->
           @yield('contents')
        </div>

        {{--  scripts --}}
        @include('files.includes.scripts')

        @yield('extra_scripts')

        <div id="snackbar"></div>
    </body>
</html>
