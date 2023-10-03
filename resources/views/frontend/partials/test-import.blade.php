{{-- For testing environment --}}
@if (config('zakirsoft.testing_mode'))
    @php
        $setting = App\Models\Setting::first();
        $cms_setting = App\Models\Cms::first()
    @endphp
@endif
{{-- For testing environment --}}
