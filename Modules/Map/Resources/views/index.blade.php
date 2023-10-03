@extends('backend.layouts.app')
@section('title')
    {{ __('map') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- MAp -->
                <div class="card">
                    <form id="" class="form-horizontal" action="{{ route('module.map.update') }}" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title" style="line-height: 36px;">{{ __('map') }}
                                </h3>
                            </div>
                        </div>
                        @php
                            $map = $setting->default_map;
                        @endphp
                        <!-- ============== for map =============== -->
                        <div class="card-body">
                            <x-website.map.map-warning/>
                            <div id="text-card" class="card-body">
                                <div class="form-group row text-left d-flex justify-content-center align-items-left">
                                    @foreach ($errors->all() as $error)
                                        <div class="col-sm-12 col-md-6 text-left text-md-center">
                                            <div class="text-left alert alert-danger alert-dismissible">
                                                {{ $error }}<br />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-group row text-center d-flex align-items-center">
                                    <div class="col-sm-12 col-md-6 text-left text-md-center">
                                        <x-forms.label name="map_type" class="" />
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <select name="map_type" class="form-control @error('map_type') is-invalid @enderror"
                                            id="">
                                            <option {{ $setting->default_map == 'leaflet' ? 'selected' : '' }}
                                                value="leaflet">
                                                {{ __('leaflet') }}
                                            </option>
                                            <option {{ $setting->default_map == 'google-map' ? 'selected' : '' }}
                                                value="google-map">
                                                {{ __('google_map') }}
                                            </option>
                                        </select>
                                        @error('map_type')
                                            <span class="invalid-feedback"
                                                role="alert"><span>{{ $message }}</span></span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- long -->
                                <div class="d-none">
                                    <div class="col-sm-12 col-md-6 text-left text-md-center">
                                        <x-forms.label name="default_long" class="" />
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <input value="{{ $setting->default_long }}" name="default_long" type="text"
                                            class="form-control @error('default_long') is-invalid @enderror"
                                            autocomplete="off" placeholder="{{ __('default_long') }}">
                                        @error('default_long')
                                            <span class="text-left invalid-feedback"
                                                role="alert"><span>{{ $message }}</span></span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Lat  -->
                                <div class="d-none">
                                    <div class="col-sm-12 col-md-6">
                                        <x-forms.label name="default_lat" class="" />
                                    </div>
                                    <div class="col-sm-12 col-md-6 text-left text-md-center">
                                        <input value="{{ $setting->default_lat }}" name="default_lat" type="text"
                                            class="form-control @error('default_lat') is-invalid @enderror"
                                            autocomplete="off" placeholder="{{ __('default_lat') }}">
                                        @error('default_lat')
                                            <span class="text-left invalid-feedback"
                                                role="alert"><span>{{ $message }}</span></span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- google map key  -->
                                <div id="googlemap_key" class="{{ $map == 'google-map' ? '' : 'd-none' }}">
                                    <div class="pt-4 form-group row text-center d-flex align-items-center">
                                        <div class="col-sm-12 col-md-6">
                                            <x-forms.label name="your_google_map_key" class="" />
                                        </div>
                                        <div class="col-sm-12 col-md-6 text-left text-md-center">
                                            <input value="{{ $setting->google_map_key }}" name="google_map_key"
                                                type="text"
                                                class="form-control @error('google_map_key') is-invalid @enderror"
                                                autocomplete="off" placeholder="{{ __('your_google_map_key') }}">
                                            @error('google_map_key')
                                                <span class="text-left invalid-feedback"
                                                    role="alert"><span>{{ $message }}</span></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!-- example map  -->
                                <div class="pt-4 form-group row text-center d-flex align-items-center">
                                    <div class="col-sm-12 col-md-6 text-left text-md-center">
                                        <x-forms.label name="example_map" class="" />
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        {{-- Leaflet --}}
                                        <div class="map mymap" id='leaflet-map'></div>

                                        {{-- Google Map --}}
                                        <div id="google-map-div" class="{{ $map == 'google-map' ? '' : 'd-none' }}">
                                            <input id="searchInput" class="mapClass" type="text"
                                                placeholder="Enter a location">
                                            <div class="map mymap" id="google-map"></div>
                                        </div>
                                        @error('location')
                                            <span class="text-md text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row pb-3">
                                <div class="offset-sm-5 col-sm-7">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-sync"></i>
                                        {{ __('update') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <!-- >=>Leaflet<=< -->
    <link href="{{ asset('backend/css/leaflet.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('backend/css/autocomplete.min.css') }}"/>
    <style>
        .mymap {
            width: 100%;
            min-height: 300px;
            border-radius: 12px;
        }

        .p-half {
            padding: 1px;
        }

        .mapClass {
            border: 1px solid transparent;
            margin-top: 15px;
            border-radius: 4px 0 0 4px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: 35px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        #searchInput {
            font-family: 'Roboto';
            background-color: #fff;
            font-size: 16px;
            text-overflow: ellipsis;
            margin-left: 16px;
            font-weight: 400;
            width: 30%;
            padding: 0 11px 0 13px;
        }

        #searchInput:focus {
            border-color: #4d90fe;
        }
    </style>
@endsection

@section('script')
    <!-- >=>Leaflet<=< -->
    <script>
        // minimal configure
        new Autocomplete("leaflet_serce", {
            selectFirst: true,
            howManyCharacters: 2,

            onSearch: ({ currentValue }) => {
                const api = `https://nominatim.openstreetmap.org/search?format=geojson&limit=5&city=${encodeURI(currentValue)}`;

                return new Promise((resolve) => {
                fetch(api)
                    .then((response) => response.json())
                    .then((data) => {
                        resolve(data.features);
                    })
                    .catch((error) => {
                        console.error(error);
                    });
                });
            },
            onResults: ({ currentValue, matches, template }) => {
                const regex = new RegExp(currentValue, "gi");
                return matches === 0
                ? template
                : matches
                    .map((element) => {
                        return `
                    <li class="loupe">
                        <p>
                        ${element.properties.display_name.replace(
                            regex,
                            (str) => `<b>${str}</b>`
                        )}
                        </p>
                    </li> `;
                    })
                    .join("");
            },

            onSubmit: ({ object }) => {
                map.eachLayer(function (layer) {
                if (!!layer.toGeoJSON) {
                    map.removeLayer(layer);
                }
                });

                const { display_name } = object.properties;
                const [lng, lat] = object.geometry.coordinates;

                const marker = L.marker([lat, lng], {
                title: display_name,
                });

                marker.addTo(map).bindPopup(display_name);

                map.setView([lat, lng], 8);
            },

            onSelectedItem: ({ index, element, object }) => {
                console.log(object.properties)
                console.log(object.geometry.coordinates)
                // console.log("onSelectedItem:", index, element, object);
            },

            // the method presents no results element
            noResults: ({ currentValue, template }) =>
            template(`<li>No results found: "${currentValue}"</li>`),
        });


        // Map preview
        var element = document.getElementById('leaflet-map');

        // Height has to be set. You can do this in CSS too.
        element.style = 'height:300px;';

        // Create Leaflet map on map element.
        var map = L.map(element);

        // Add OSM tile layer to the Leaflet map.
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Target's GPS coordinates.
        var target = L.latLng('47.50737', '19.04611');

        // Set map's center to target with zoom 14.
        const zoom = 14;
        map.setView(target, zoom);

        // Place a marker on the same location.
        L.marker(target).addTo(map);

    </script>
    <!-- >=>Google Map<=< -->
    <x-website.map.google-map-check/>
    <script>
        function initMap() {
            var token = "{{ $setting->google_map_key }}";
            var oldlat = {{ $setting->default_lat }};
            var oldlng = {{ $setting->default_long }};

            const map = new google.maps.Map(document.getElementById("google-map"), {
                zoom: 7,
                center: {
                    lat: oldlat,
                    lng: oldlng
                },
            });

            const image =
                "https://gisgeography.com/wp-content/uploads/2018/01/map-marker-3-116x200.png";
            const beachMarker = new google.maps.Marker({

                draggable: true,
                position: {
                    lat: oldlat,
                    lng: oldlng
                },
                map,
                // icon: image
            });

            google.maps.event.addListener(map, 'click',
                function(event) {
                    pos = event.latLng
                    beachMarker.setPosition(pos);
                    let lat = beachMarker.position.lat();
                    let lng = beachMarker.position.lng();

                    $('input[name="default_lat"]').val(lat);
                    $('input[name="default_long"]').val(lng);
                });

            google.maps.event.addListener(beachMarker, 'dragend',
                function() {
                    let lat = beachMarker.position.lat();
                    let lng = beachMarker.position.lng();

                    $('input[name="default_lat"]').val(lat);
                    $('input[name="default_long"]').val(lng);
                });

            // Search
            var input = document.getElementById('searchInput');
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            var autocomplete = new google.maps.places.Autocomplete(input);
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
    <script>
        $('select[name="map_type"]').on('change', function() {
            var value = $(this).val();
            if (value == 'google-map') {
                $('#google-map-div').removeClass('d-none');
                $('#googlemap_key').removeClass('d-none');
                $('#leaflet-map').removeClass('d-none');
            } else {
                $('#google-map-div').addClass('d-none');
                $('#googlemap_key').addClass('d-none');
                $('#leaflet-map').addClass('d-none');
            }
        })
    </script>
@endsection
