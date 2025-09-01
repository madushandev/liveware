<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'New Page Title' }}</title>
        @vite('resources/css/app.css')
        @vite('resources/js/main.js')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    </head>
    <body>
        {{ $slot }}
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    </body>
</html>
