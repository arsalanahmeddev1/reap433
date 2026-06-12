<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.admin.partials.head')
</head>

<body class="dark-only position-relative">
    @php
        $company = auth()->user()->company ?? null;
    @endphp
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>

    <!-- <div class="tap-top"><i data-feather="chevrons-up"></i></div> -->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include('layouts.admin.partials.header')
        <div class="page-body-wrapper">
            @include('layouts.admin.partials.sidebar')
            <div class="page-body">
                @include('layouts.admin.partials.bread_crumbs', ['breadcrumbs' => $breadcrumbs ?? []])
                @yield('content')
            </div>
        </div>
    </div>
    @include('layouts.admin.partials.scripts')
</body>

</html>
