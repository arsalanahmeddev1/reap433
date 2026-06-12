<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/web/images/logo.png') }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ asset('assets/web/images/logo.png') }}" type="image/x-icon" />
    <title>@yield('title', __('Sign in')) | REAP433</title>

    <link href="https://fonts.googleapis.com/css?family=Rubik:400,500,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />
</head>

<body class="dark-only">
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="login-card login-dark reap433-login">
                    <div>
                        <div>
                            <a class="logo" href="{{ route('home') }}">
                                <img
                                    class="img-fluid reap433-admin-logo mx-auto d-block"
                                    src="{{ asset('assets/web/images/logo.png') }}"
                                    alt="REAP433"
                                />
                            </a>
                            <div class="login-main">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        (function($) {
            $('.show-hide span').on('click', function() {
                var input = $(this).closest('.form-input').find('input');
                if ($(this).hasClass('show')) {
                    input.attr('type', 'text');
                    $(this).removeClass('show').text('Hide');
                } else {
                    input.attr('type', 'password');
                    $(this).addClass('show').text('Show');
                }
            });
        })(jQuery);
    </script>
    @stack('scripts')
</body>

</html>
