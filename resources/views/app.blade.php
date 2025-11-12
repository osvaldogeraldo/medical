<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Favicon --}}
    <link rel="icon" href="/template/images/favicon.svg" type="image/svg+xml">

    {{-- Template CSS --}}
    <link rel="stylesheet" href="/template/css/main.min.css">
    <link rel="stylesheet" href="/template/vendor/overlay-scroll/OverlayScrollbars.min.css">

    {{-- Vite --}}
    @vite('resources/js/app.js')

    {{-- Inertia --}}
    @inertiaHead
</head>
<body>
    @inertia

    {{-- Template JS --}}
    <script src="/template/js/jquery.min.html"></script>
    <script src="/template/js/bootstrap.bundle.min.html"></script>
    <script src="/template/js/custom.html"></script>
    <script src="/template/js/moment.min.html"></script>
    <script src="/template/js/table-random-colors.html"></script>
</body>
</html>
