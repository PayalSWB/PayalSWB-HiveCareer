@if ($setting->default_layout)
    @include('backend.layouts.left-nav')
@else
    @include('backend.layouts.top-nav')
@endif
<input type="hidden" value="{{ current_country_code() }}" id="current_country_code">
