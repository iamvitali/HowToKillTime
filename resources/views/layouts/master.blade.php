<!DOCTYPE html>
<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Is needed for phones to scale it appropriately, otherwise it will be zoomed out --}}
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

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

                /* Laravel specific, making AJAX queries work */
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                @yield('documentReadyJquery');
            });
        </script>
    </body>
</html>