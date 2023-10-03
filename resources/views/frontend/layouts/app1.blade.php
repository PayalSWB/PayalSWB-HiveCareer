<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description')">
    <meta property="og:image" content="@yield('og:image')">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <title>@yield('title') - {{ config('app.name') }}</title>
    @yield("ld-data")

    {{-- Style --}}
    @include('frontend.partials.analytics')
    @include('frontend.partials.links')
    @include('frontend.partials.backend')
    @yield('css')

    {{-- Custome css and js  --}}
    {!! $setting->header_css !!}
    {!! $setting->header_script !!}
</head>

<body dir="{{ langDirection() }}">
    <input type="hidden" value="{{ current_country_code() }}" id="current_country_code">
    <x-admin.app-mode-alert />
    {{-- Header --}}
    @include('frontend.partials.header')

    {{-- Main --}}
    @yield('main')

    {{-- footer --}}
    @if (!Route::is('candidate.*') && !Route::is('company.*'))
        @include('frontend.partials.footer')
    @endif

    <!-- scripts -->
    @include('frontend.partials.scripts')

    <!-- Custom js -->
    {!! $setting->body_script !!}

    <x-frontend.cookies-allowance :cookies="$cookies" />
</body>

</html>
