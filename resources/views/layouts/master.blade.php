<!DOCTYPE html>
<html>
    <head>
        <title>@yield('title')</title>

        <link href="/css/app.css" rel="stylesheet">
        <style type="text/css">
            @yield('css')
        </style>

    </head>
    <body>
        <div class="container">
            @yield('content')
        </div>

        {{ Html::script('/js/jquery-3.1.0.min.js') }}
        {{ Html::script('/js/bootstrap.min.js') }}
        @yield('additionalScripts')

        <script>
            $(document).ready(function() {
                @yield('documentReadyJquery');
            });
        </script>
    </body>
</html>