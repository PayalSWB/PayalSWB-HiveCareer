@extends('backend.layouts.app')
@section('title')
    {{ __('state_list') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title line-height-36">
                            {{ __('state_list') }}
                            <span class="ml-1 badge bg-primary">
                                {{ $states->total() }}
                            </span>
                        </h3>
                        <div class="align-items-center  ml-auto">
                            @if (userCan('state.create'))
                                <a href="{{ route('module.state.create') }}"
                                    class="btn bg-primary float-right d-flex align-items-center justify-content-center">
                                    <i class="fas fa-plus"></i>
                                    &nbsp; {{ __('create') }}
                                </a>
                            @endif
                            <button id="DeleteButton" onclick="MultiDelete()" data-toggle="tooltip" data-placement="top"
                                title="{{ __('delete_selected_countries') }}"
                                class="d-none mr-3 btn bg-danger float-right align-items-center justify-content-center">
                                <i class="fas fa-trash"></i>
                                <span class="ml-1">
                                    {{ __('delete') }}
                                    <span id="selectedCount"
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info">
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <form action="{{ route('module.state.index') }}" method="GET">
                        <div class="row justify-content-end">
                            <div class="col-md-8 col-sm-10 col-lg-6 col-12">
                                <div class="d-flex justify-space-between px-3 my-3">
                                    <input type="text"
                                        @if (request('name')) value="{{ request('name') }}" @endif id="name"
                                        class="form-control mr-2" placeholder="{{ __('enter') }} {{ __('search') }}..."
                                        name="name" aria-label="">
                                    <select name="country" class="form-control form-control mr-2">
                                        <option value="">{{ __('all_country') }}</option>
                                        @foreach ($allCountries as $country)
                                            <option {{ request('country') == $country->id ? 'selected' : '' }}
                                                value="{{ $country->id }}" class="">
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-primary btn bg-primary" type="submit">
                                        {{ __('search') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="">
                        <div class="dataTables_wrapper dt-bootstrap4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline"
                                        role="grid" aria-describedby="example1_info">
                                        <thead>
                                            <tr role="row" class="text-center">
                                                @if ($states->count() > 1)
                                                    <th width="4%">
                                                        <div class="icheck-primary ml-1">
                                                            <input type="checkbox" id="checkboxAll">
                                                            <label for="checkboxAll">
                                                            </label>
                                                        </div>
                                                    </th>
                                                @endif
                                                <th>
                                                    #
                                                </th>
                                                <th>{{ __('name') }}
                                                </th>
                                                <th> {{ __('city_count') }}</th>
                                                <th>{{ __('country') }}
                                                </th>
                                                @if (userCan('state.update') || userCan('state.delete'))
                                                    <th>
                                                        {{ __('action') }}
                                                    </th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($states->count() > 0)
                                                @foreach ($states as $state)
                                                    <tr role="row" class="text-center">
                                                        @if ($states->count() > 1)
                                                            <td class="text-center items-center">
                                                                <div class="icheck-primary ml-1">
                                                                    <input type="checkbox" data-id={{ $state->id }}
                                                                        class="sub_chk"
                                                                        id="checkbox{{ $state->id }}">
                                                                    <label for="checkbox{{ $state->id }}">
                                                                    </label>
                                                                </div>
                                                            </td>
                                                        @endif
                                                        <td class="text-center">
                                                            {{ $state->id }}
                                                        </td>
                                                        <td class="text-primary sorting_1 text-center" tabindex="0">
                                                            <a
                                                                href="{{ route('module.city.index', ['state' => $state->id]) }}">
                                                                {{ $state->name }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a
                                                                href="{{ route('module.city.index', ['state' => $state->id]) }}">
                                                                ({{ $state->cities_count }})
                                                            </a>
                                                        </td>
                                                        <td class="sorting_1 text-center" tabindex="0">
                                                            <form action="{{ route('module.state.index') }}"
                                                                method="GET">
                                                                <input type="hidden" name="country"
                                                                    value="{{ request('country') ? request('country') : $state->country->id }}">
                                                                <button class="btn text-primary" type="submit">
                                                                    {{ $state->country->name }}
                                                                </button>
                                                            </form>
                                                        </td>
                                                        @if (userCan('state.update') || userCan('state.delete'))
                                                            <td class="sorting_1 text-center" tabindex="0">
                                                                @if (userCan('state.update'))
                                                                    <a data-toggle="tooltip" data-placement="top"
                                                                        title="
                                                                                                                                                                {{ __('edit') }}"
                                                                        href="{{ route('module.state.edit', $state->id) }}"
                                                                        class="btn bg-info">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                @endif
                                                                @if (userCan('state.delete'))
                                                                    <form
                                                                        action="{{ route('module.state.delete', $state->id) }}"
                                                                        method="POST" class="d-inline">
                                                                        @method('DELETE')
                                                                        @csrf
                                                                        <button data-toggle="tooltip" data-placement="top"
                                                                            title="{{ __('delete') }}"
                                                                            onclick="return confirm('{{ __('are_you_sure_you_want_to_delete_this_item') }}');"
                                                                            class="btn bg-danger">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center" colspan="4">{{ __('no_data_found') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if (request('perpage') != 'all' && $states->total() > $states->count())
                                <div class="mt-3 d-flex justify-content-center">
                                    {{ $states->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <style>
        /* Extra small devices (phones, 600px and down) */
        @media only screen and (max-width: 600px) {
            .customRow1 {
                margin-top: 10px;
            }

            .customRow2 {
                margin-top: 10px;
                padding-left: 15px;
                padding-right: 15px;
            }

            .customdiv1 {
                margin-top: 5px;
                margin-right: .5rem !important;
            }

            .customdiv2 {
                margin-top: 5px;
                margin-right: .5rem !important;
            }

            .customdiv3 {
                margin-top: 5px;
                margin-bottom: 5px;
                margin-right: .5rem !important;
            }
        }
    </style>
@endsection
@section('script')
    <script>
        $("#checkboxAll").on('click', function() {
            $('input:checkbox').not(this).prop('checked', this.checked);
            SelectIds();
        });
        $(".sub_chk").on('click', function() {
            SelectIds();
        });

        function SelectIds() {

            var Ids = [];
            $(".sub_chk:checked").each(function() {
                Ids.push($(this).attr('data-id'));
            });
            $('#selectedCount').html(parseInt(Ids.length));
            if (Ids != 0) {
                // Show Delete Button
                $('#DeleteButton').removeClass('d-none');
            } else {
                // Hide Delete Button
                $('#DeleteButton').addClass('d-none');
            }
        }

        function MultiDelete() {

            var Ids = [];
            $(".sub_chk:checked").each(function() {
                Ids.push($(this).attr('data-id'));
            });

            if (Ids != 0) {

                if (confirm("{{ __('are_you_sure_you_want_to_delete_this_item') }}") == true) {
                    AjaxCall(Ids);
                } else {
                    return false;
                }

            } else {
                alert('Please Select First');
            }
        }

        function AjaxCall(value) {

            $.ajax({
                url: "{{ route('module.state.multiple.delete') }}",
                type: "Delete",
                data: {
                    ids: value,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    location.reload();
                }
            });
        };
    </script>
@endsection
