<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Application Portal') — Tima-Ade University</title>
    <meta name="description" content="@yield('meta_description', 'Tima-Ade University Application Portal')">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    @stack('styles')
</head>
<body class="public-body application-portal-body">
    <header class="application-portal-header">
        <div class="container application-portal-header-inner">
            <a class="application-portal-brand" href="{{ route('public.home') }}" aria-label="Return to Tima-Ade University website">
                <img src="{{ asset('images/tima-ade-university-logo-premium.svg') }}" alt="Tima-Ade University logo" class="brand-logo-image">
                <span>Tima-Ade University</span>
            </a>
            <a class="application-portal-exit" href="{{ route('public.home') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> University website</a>
        </div>
    </header>

    <main>
        @include('partials._alerts')
        @yield('content')
    </main>

    <footer class="application-portal-footer">
        <div class="container">Tima-Ade University <span aria-hidden="true">&middot;</span> Application Portal</div>
    </footer>
</body>
</html>
