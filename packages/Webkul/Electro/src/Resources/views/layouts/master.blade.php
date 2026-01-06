<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>

    {{-- Load theme assets manually --}}
    @bagistoVite([
        'src/Resources/assets/css/app.css',
        'src/Resources/assets/js/app.js'
    ])

    {{-- Additional meta tags --}}
    @stack('meta')

    {{-- Additional styles --}}
    @stack('styles')
</head>
<body class="{{ $bodyClass ?? '' }}">
{{-- Custom header --}}
@if($hasHeader ?? true)
    @include('Electro::layouts.header')
@endif

{{-- Main content --}}
<main class="main-content">
    {{ $slot }}
</main>

{{-- Custom footer --}}
@if($hasFooter ?? true)
    @include('Electro::layouts.footer')
@endif

{{-- Additional scripts --}}
@stack('scripts')
</body>
</html>
