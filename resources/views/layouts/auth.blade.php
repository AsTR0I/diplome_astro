<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Личный Кабинет') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}?u20201001" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&amp;subset=cyrillic">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700&amp;subset=cyrillic">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @section('styles')
    <style>
        html, body {
            background: #f5f5f5;
            min-height: 100%;
        }

        .auth-container {
            margin: 80px auto 0;
            width: 300px;
        }

        .text-muted {
            color: rgba(0, 0, 0, .5);
        }

        .logo {
            display: block;
            margin: 0 auto 1rem;
            max-width: 100%;
        }

        h1 a {
            color: inherit;
            text-decoration: none;
        }
    </style>
    @show
</head>
<body class="v-application theme--cocobri v-application--is-ltr theme--light">
    <div class="v-application--wrap">
        <div class="auth-container">
            <h1 class="display-2 text-center mb-2 primary--text" style="font-family: 'Montserrat', sans-serif !important">
                <a href="/">ASTRO ATC</a>
            </h1>
            <h2 class="subtitle-2 text-center mb-8 primary--text" style="font-family: 'Montserrat', sans-serif !important">
                Личный Кабинет
            </h2>

            @yield('content')
        </div><!-- /.auth-container -->
    </div>
</body>
</html>
