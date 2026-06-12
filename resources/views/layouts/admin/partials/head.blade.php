<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description"
    content="web app" />
<meta name="keywords"
    content="web app" />
<meta name="author" content="pixelstrap" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset('assets/web/images/logo.png') }}" type="image/x-icon" />
<link rel="shortcut icon" href="{{ asset('assets/web/images/logo.png') }}" type="image/x-icon" />

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Rubik:400,500,700&display=swap" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet" />

<script>
    (function () {
    let theme = localStorage.getItem('theme') || 'dark';

    document.addEventListener('DOMContentLoaded', function () {
        if (theme === 'dark') {
            document.body.classList.add('dark-only');
        } else {
            document.body.classList.remove('dark-only');
        }
    });
})();
</script>

<!-- Vendor CSS -->
<link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/fontawesome.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/icofont.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/themify.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/flag-icon.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/feather-icon.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/slick.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/slick-theme.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/scrollbar.css') }}" />
<link rel="preload" as="script" href="{{ asset('assets/libs/js/scrollbar/simplebar.min.js') }}">
<script src="{{ asset('assets/libs/js/scrollbar/simplebar.min.js') }}"></script>
<link href="https://cdn.jsdelivr.net/npm/dropzone@5.9.2/dist/min/dropzone.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/animate.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/bootstrap.css') }}" />
<!-- Main CSS -->
<link rel="stylesheet" href="{{ asset('assets/libs/css/style.css') }}" />
<link id="color" rel="stylesheet" href="{{ asset('assets/libs/css/color-1.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/css/responsive.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/libs/css/vendors/jquery.dataTables.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />
@stack('styles')


<script defer src="{{ asset('assets/libs/css/color-1.js') }}"></script>
<script defer src="{{ asset('assets/libs/css/responsive.js') }}"></script>
<script defer src="{{ asset('assets/libs/css/style.js') }}"></script>

<title>
    @hasSection('title')
    @yield('title') | REAP433
    @else
    Dashboard | REAP433
    @endif

    @hasSection('page_heading')
        @yield('page_heading')
    @else
        {{ $roleName ?? 'Dashboard' }}
    @endif

</title>
