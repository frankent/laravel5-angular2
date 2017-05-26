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

        <div class="header_menu">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <img src="{{ asset("images/logo_white.png")  }}" class="img-responsive">
                    </div>
                </div>
            </div>
        </div>

        <div class='container' style="padding-top: 20px;">

            <my-app>Loading...</my-app>

            <div class='row' style="padding-top: 20px;">
                <div class='col-md-12 light_blue clearfix'>
                    <div style='float: left;'>
                        <img src="{{ asset("images/footer_logo.png")  }}">
                    </div>
                    <div style='float: left; margin-left: 6px; font-size: 12px;'>
                        <p style='margin: 8px 0px 0px;'>(2014 - {{ date('Y') }}) by Regional Irrigation Office 1 of Royal Irrigation Department, Thailand</p>
                        <p style='margin: 0px;'>Brought to you by TryCatch&trade;</p>
                    </div>
                </div>
            </div>
        </div>
        @if (App::environment('production'))
            <script src="{{ elixir('js/all.js') }}"></script>
        @else
            <script src="{{ elixir('js/vendor.js') }}"></script>
            <script src="{{ elixir('js/app.js') }}"></script>
        @endif
    </body>
</html>
