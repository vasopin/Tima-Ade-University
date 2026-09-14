<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tima-Ade University — @yield('title', 'Welcome')</title>
    <meta name="description" content="Tima-Ade University role welcome page.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        html {
            min-width: 320px;
            scrollbar-gutter: auto;
            overflow-x: hidden;
            overflow-y: hidden;
        }

        body {
            min-width: 320px;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            overflow-y: hidden;
            background: #f8fafc;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')
</body>
</html>
