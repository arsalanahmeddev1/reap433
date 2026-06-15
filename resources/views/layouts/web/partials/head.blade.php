<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>@yield('title', '') | REAP433</title>
<link rel="icon" href="{{ asset('assets/web/images/favicon.ico') }}" type="image/x-icon">
<meta name="description" content="REAP433 is a bold lifestyle and civic leadership brand from Grand Prairie, Texas. Premium trademarked merchandise meets infinite civic impact." />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Syne:wght@400;600;700;800&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/web/css/style.css') }}" />
@stack('styles')