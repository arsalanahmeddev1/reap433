<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.web.partials.head')
</head>

<body>
    @include('layouts.web.partials.header')
    @yield('content')
    @include('layouts.web.partials.footer')
    @include('layouts.web.partials.scripts')

</body>

</html>