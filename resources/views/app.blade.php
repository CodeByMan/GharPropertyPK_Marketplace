<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#e8f0e3">
    <meta name="description" content="GharProperty.pk is a modern Pakistan property marketplace developed by Muhammad Ali Nawaz.">
    <title>{{ config('app.name', 'GharProperty.pk') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @routes
    @vite('resources/js/app.js')
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
