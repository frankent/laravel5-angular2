<!DOCTYPE html>
<html lang="en">
    <head>
        <base href='/'>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo isset($title) ? $title : "ระบบรายงานสภาพลุ่มน้ำ" ?> | ลุ่มแม่น้ำแม่งอน</title>

        @if (App::environment('production'))
            <link rel="stylesheet" type="text/css" href="{{ elixir('css/all.css') }}">
        @else
            <link rel="stylesheet" type="text/css" href="{{ elixir('css/app.css') }}">
            <link rel="stylesheet" type="text/css" href="{{ elixir('css/maengud.css') }}">
        @endif

    </head>
    <body>

        <my-app>
            <div class="center-all">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
            <style>
                html, body {
                    width: 100%;
                    height: 100%;
                }

                .center-all {
                    padding: 50%;
                    margin: -8px;
                    width: 16px;
                    height: 16px;
                }
            </style>
        </my-app>

        @if (App::environment('production'))
            <script src="{{ elixir('js/all.js') }}"></script>
        @else
            <script src="{{ elixir('js/vendor.js') }}"></script>
            <script src="{{ elixir('js/app.js') }}"></script>
        @endif
    </body>
</html>
