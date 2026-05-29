<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.admin.partials.head')
</head>

<body onload="startTime()">
    <div class="loader-wrapper">
        <div class="loader-index"><span></span></div>
        <svg>
            <defs></defs>
            <filter id="goo">
                <fegaussianblur
                    in="SourceGraphic"
                    stddeviation="11"
                    result="blur"></fegaussianblur>
                <fecolormatrix
                    in="blur"
                    values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9"
                    result="goo"></fecolormatrix>
            </filter>
        </svg>
    </div>
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include('layouts.admin.partials.header')
        <div class="page-body-wrapper">
            @include('layouts.admin.partials.sidebar')
            <div class="page-body">
                <div class="container-fluid">
                    @include('layouts.admin.partials.bread_crumbs')
                    @yield('content')
                </div>
            </div>
            @include('layouts.admin.partials.footer')
        </div>
    </div>
    @include('layouts.admin.partials.scripts')
</body>

</html>