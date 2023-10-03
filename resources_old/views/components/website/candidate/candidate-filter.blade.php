@props(['professions', 'experiences', 'educations', 'skills'])

<form id="form" action="{{ route('website.candidate') }}" method="GET">
    <div class="breadcrumbs style-two">
        <div class="container">
            <div class="row align-items-center ">
                <div class="col-12 position-relative">
                    <div class="breadcrumb-menu mb-4">
                        <h6 class="f-size-18 m-0">{{ __('find_candidates') }}</h6>
                        <ul>
                            <li><a href="{{ route('website.home') }}">{{ __('home') }}</a></li>
                            <li>/</li>
                            <li>{{ __('candidates') }}</li>
                        </ul>
                    </div>
                    <div class="jobsearchBox  bg-gray-10 input-transparent height-auto-lg">
                        <div class="top-content d-flex flex-column flex-lg-row align-items-center leaflet-map-results">
                            <div class="flex-grow-3 fromGroup has-icon banner-select">
                                <select class="rt-selectactive w-100-p" name="profession">
                                    <option value="" class="d-none">{{ __('select_profession') }}</option>
                                    @foreach ($professions as $profession)
                                        <option {{ $profession->id == request('profession') ? 'selected' : '' }}
                                            value="{{ $profession->id }}">
                                            {{ $profession->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="icon-badge category-icon">
                                    <x-svg.layer-icon stroke="var(--primary-500)" width="24" height="24" />
                                </div>
                            </div>

                            <input type="hidden" name="lat" id="lat" value="">
                            <input type="hidden" name="long" id="long" value="">
                            @php
                                $oldLocation = request('location');
                                $map = $setting->default_map;
                            @endphp

                            @if ($map == 'google-map')
                                <div class="inputbox_2 fromGroup has-icon">
                                    <input type="text" id="searchInput" placeholder="Enter a location..."
                                        name="location" value="{{ $oldLocation }}" />
                                    <div id="google-map" class="d-none"></div>
                                    <div class="icon-badge">
                                        <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}"
                                            width="24" height="24" />
                                    </div>
                                </div>
                            @else
                                <div class="inputbox_2 fromGroup has-icon">
                                    <input name="long" class="leaf_lon" type="hidden">
                                    <input name="lat" class="leaf_lat" type="hidden">
                                    <input type="text" id="leaflet_search" placeholder="{{ __('enter_location') }}"
                                        name="location" value="{{ request('location') }}" class="tw-border-0"
                                        autocomplete="off" />

                                    <div class="icon-badge">
                                        <x-svg.location-icon stroke="{{ $setting->frontend_primary_color }}"
                                            width="24" height="24" />
                                    </div>
                                </div>
                            @endif
                            <div class="inputbox_2 fromGroup has-icon">
                                <select name="status" class="rt-selectactive gap w-100-p">
                                    <option {{ request('status') ? '' : 'selected' }} value="">
                                        {{ __('all_status') }}
                                    </option>
                                    <option {{ request('status') == 'available' ? 'selected' : '' }} value="available">
                                        {{ __('available') }}
                                    </option>
                                    <option {{ request('status') == 'not_available' ? 'selected' : '' }}
                                        value="not_available">
                                        {{ __('not_available') }}
                                    </option>
                                </select>
                                <div class="icon-badge category-icon">
                                    <x-svg.briefcase-icon stroke="var(--primary-500)" width="24" height="24" />
                                </div>
                            </div>

                            <div class="flex-grow-0 rt-pt-md-20">
                                <button
                                    class="btn btn-primary d-block d-md-inline-block ">{{ __('search_candidates') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="candidate-content">
        <div class="container">
            <!-- ============ Filter Old Data ==========  -->
            <div class="d-flex w-100-p">
                @if (Request::get('keyword'))
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('keyword') }}"
                            filter="{{ request('keyword') }}" />
                    </div>
                @endif
                @if (Request::get('country'))
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('country') }}"
                            filter="{{ request('country') }}" />
                    </div>
                @endif
                @if (Request::get('sortby') && Request::get('sortby') != 'latest')
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('sortby') }}"
                            filter="{{ request('sortby') }}" />
                    </div>
                @endif
                @if (Request::get('profession') && Request::get('profession') != null)
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('profession') }}"
                            filter="{{ $professions->where('id', request('profession'))->first()->name ?? '-' }}" />
                    </div>
                @endif
                @if (Request::get('experience') && Request::get('experience') != 'all')
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('experience') }}"
                            filter="{{ request('experience') }}" />
                    </div>
                @endif
                @if (Request::get('gender') && Request::get('gender') != 'all')
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('gender') }}"
                            filter="{{ request('gender') }}" />
                    </div>
                @endif
                @if (Request::get('education') && Request::get('education') != 'all')
                    <div class="rt-mr-2 icon-badge mt-3">
                        <x-website.candidate.filter-data-component title="{{ __('education') }}"
                            filter="{{ request('education') }}" />
                    </div>
                @endif
            </div>
            <!-- ============ Filter Old Data End ==========  -->
            <div class="row">
                <div class="col-lg-12 rt-mb-24">
                    <div class="joblist-left-content2 rt-pt-50">
                        <div class="tw-flex tw-justify-between tw-items-center rt-mb-24">
                            <button type="button" class="btn btn-primary-50 toggole-colum-classes">
                                <span class="button-content-wrapper ">
                                    <span class="button-icon align-icon-left">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20 21V16" stroke="var(--primary-500)" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M17 16H23" stroke="var(--primary-500)" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M4 21V14" stroke="var(--primary-500)" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M1 14H7" stroke="var(--primary-500)" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M12 21V12" stroke="var(--primary-500)" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M9 8H15" stroke="var(--primary-500)" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M20 12V3" stroke="var(--primary-500)" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M12 8V3" stroke="var(--primary-500)" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M4 10V3" stroke="var(--primary-500)" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <span class="button-text">
                                        {{ __('filter') }}
                                    </span>
                                </span>
                            </button>
                            <div class="joblist-fliter-gorup !tw-min-w-max">
                                <div class="right-content !tw-mt-0">
                                    <nav>
                                        <div class="nav" id="nav-tab" role="tablist">
                                            <button onclick="styleSwitch('box')" class="nav-link active "
                                                id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                                                type="button" role="tab" aria-controls="nav-home"
                                                aria-selected="true">
                                                <x-svg.box-icon />
                                            </button>
                                            <button onclick="styleSwitch('list')" class="nav-link"
                                                id="nav-profile-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-profile" type="button" role="tab"
                                                aria-controls="nav-profile" aria-selected="false">
                                                <x-svg.list-icon />
                                            </button>
                                        </div>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="rt-spacer-10"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4  @if (request('education') || request('gender') || request('experience') || request('skills')) @else d-none @endif rt-mb-lg-30"
                    id="toggoleSidebar">
                    <div class="togglesidebr_widget">
                        <div class="sidetbar-widget !tw-overflow-x-auto">
                            <ul>
                                <li class="d-block has-children open">
                                    <div class="jobwidget_tiitle">{{ __('skills') }}</div>
                                    <ul class="sub-catagory">
                                        <li class="d-block">
                                            <div class="benefits-tags">
                                                @foreach ($skills as $skill)
                                                    <label for="{{ $skill->name }}" class="py-1">
                                                        <input onclick="Filter()"
                                                            {{ request('skills') ? (in_array($skill->id, request('skills')) ? 'checked' : '') : '' }}
                                                            type="checkbox" id="{{ $skill->name }}"
                                                            value="{{ $skill->id }}" name="skills[]">
                                                        <span>{{ $skill->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                <li class="d-block has-children open">
                                    <div class="jobwidget_tiitle">{{ __('experience') }}</div>
                                    <ul class="sub-catagory">
                                        <li class="d-block tw-py-1">
                                            <div class="form-check from-radio-custom tw-flex tw-items-center">
                                                <input id="experienceall" class="form-check-input"
                                                    {{ request('experience') == 'all' ? 'checked' : '' }}
                                                    type="radio" name="experience" value="all">
                                                <label class="form-check-label pointer text-gray-700 f-size-14 tw-mt-1"
                                                    for="experienceall">
                                                    {{ __('all') }}
                                                </label>
                                            </div>
                                        </li>
                                        @foreach ($experiences as $experience)
                                            <li class="d-block tw-py-1">
                                                <div class="form-check from-radio-custom tw-flex tw-items-center">
                                                    <input class="form-check-input"
                                                        {{ request('experience') == $experience->name ? 'checked' : '' }}
                                                        type="radio" name="experience"
                                                        value="{{ $experience->name }}"
                                                        id="{{ $experience->slug }}">
                                                    <label
                                                        class="form-check-label pointer text-gray-700 f-size-14 tw-mt-1"
                                                        for="{{ $experience->slug }}">
                                                        {{ $experience->name }}
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                                <li class="d-block has-children open">
                                    <div class="jobwidget_tiitle">{{ __('education') }}</div>
                                    <ul class="sub-catagory">
                                        <li class="d-block">
                                            <div class="form-check from-radio-custom tw-flex tw-items-center">
                                                <input {{ request('education') == 'all' ? 'checked' : '' }}
                                                    name="education" class="form-check-input" type="radio"
                                                    value="all" id="educationall">
                                                <label class="form-check-label pointer text-gray-700 f-size-14 tw-mt-1"
                                                    for="educationall">
                                                    {{ __('all') }}
                                                </label>
                                            </div>
                                        </li>
                                        @foreach ($educations as $education)
                                            <li class="d-block tw-py-1">
                                                <div class="form-check from-radio-custom tw-flex tw-items-center">
                                                    <input
                                                        {{ request('education') == $education->name ? 'checked' : '' }}
                                                        name="education" class="form-check-input" type="radio"
                                                        value="{{ $education->name }}" id="{{ $education->slug }}">
                                                    <label
                                                        class="form-check-label pointer text-gray-700 f-size-14 tw-mt-1"
                                                        for="{{ $education->slug }}">
                                                        {{ $education->name }}
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
</form>

@section('frontend_links')
    @include('map::links')
    <x-map.leaflet.autocomplete_links />
@endsection

@section('frontend_scripts')
    <x-map.leaflet.autocomplete_scripts />
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        $('input[type=radio]').on('change', function() {
            $('#form').submit();
        });
    </script>
    <!-- ============== gooogle map ========== -->
    @if ($map == 'google-map')
        <script>
            function initMap() {
                var token = "{{ $setting->google_map_key }}";
                var oldlat = {{ Session::has('location') ? Session::get('location')['lat'] : $setting->default_lat }};
                var oldlng = {{ Session::has('location') ? Session::get('location')['lng'] : $setting->default_long }};
                const map = new google.maps.Map(document.getElementById("google-map"), {
                    zoom: 7,
                    center: {
                        lat: oldlat,
                        lng: oldlng
                    },
                });
                // Search
                var input = document.getElementById('searchInput');
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                let country_code = '{{ current_country_code() }}';
                if (country_code) {
                    var options = {
                        componentRestrictions: {
                            country: country_code
                        }
                    };
                    var autocomplete = new google.maps.places.Autocomplete(input, options);
                } else {
                    var autocomplete = new google.maps.places.Autocomplete(input);
                }

                autocomplete.bindTo('bounds', map);
                var infowindow = new google.maps.InfoWindow();
                var marker = new google.maps.Marker({
                    map: map,
                    anchorPoint: new google.maps.Point(0, -29)
                });
                autocomplete.addListener('place_changed', function() {
                    infowindow.close();
                    marker.setVisible(false);
                    var place = autocomplete.getPlace();
                    const total = place.address_components.length;
                    let amount = '';
                    if (total > 1) {
                        amount = total - 2;
                    }
                    const result = place.address_components.slice(amount);
                    let country = '';
                    let region = '';
                    for (let index = 0; index < result.length; index++) {
                        const element = result[index];
                        if (element.types[0] == 'country') {
                            country = element.long_name;
                        }
                        if (element.types[0] == 'administrative_area_level_1') {
                            const str = element.long_name;
                            const first = str.split(',').shift()
                            region = first;
                        }
                    }
                    const text = region + ',' + country;
                    $('#insertlocation').val(text);
                    $('#lat').val(place.geometry.location.lat());
                    $('#long').val(place.geometry.location.lng());
                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }
                });
            }
            window.initMap = initMap;
        </script>
        <script>
            @php
                $link1 = 'https://maps.googleapis.com/maps/api/js?key=';
                $link2 = $setting->google_map_key;
                $Link3 = '&callback=initMap&libraries=places,geometry';
                $scr = $link1 . $link2 . $Link3;
            @endphp;
        </script>
        <script src="{{ $scr }}" async defer></script>
    @endif
@endsection
