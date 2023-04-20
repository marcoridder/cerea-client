@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('NTRIP profiles') }}
                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addModal">
                        {{ __('Add NTRIP profile') }}
                    </button>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>{{ session('status') }}</strong>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <form method="post">
                        @csrf
                        @foreach($ntripProfiles as $key => $ntripProfile)
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        {{ $ntripProfile['name'] }}
                                        <a
                                            href="#"
                                            class="btn btn-danger float-right js-delete-ntrip"
                                            data-name="{{ $ntripProfile['name'] }}"
                                            data-delete-url="{{ route('ntrip.delete', $ntripProfile['name']) }}"
                                        >
                                            <i class="fa fa-trash-alt" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group container">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="input_{{$key}}_name">
                                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('name') }}</span>
                                                        </label>
                                                        <input type="text" class="form-control mt-n3" name="profiles[{{$key}}][name]" id="input_{{$key}}_name" value="{{ $ntripProfile['name'] }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="input_{{$key}}_host">
                                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('host') }}</span>
                                                        </label>
                                                        <input type="text" class="form-control mt-n3" name="profiles[{{$key}}][host]" id="input_{{$key}}_host" value="{{ $ntripProfile['host'] }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="input_{{$key}}_port">
                                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('port') }}</span>
                                                        </label>
                                                        <input type="text" class="form-control mt-n3" name="profiles[{{$key}}][port]" id="input_{{$key}}_port" value="{{ $ntripProfile['port'] }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="input_{{$key}}_mountpoint">
                                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('mountpoint') }}</span>
                                                        </label>
                                                        <input type="text" class="form-control mt-n3" name="profiles[{{$key}}][mountpoint]" id="input_{{$key}}_mountpoint" value="{{ $ntripProfile['mountpoint'] }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="input_{{$key}}_username">
                                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('username') }}</span>
                                                        </label>
                                                        <input type="text" class="form-control mt-n3" name="profiles[{{$key}}][username]" id="input_{{$key}}_username" value="{{ $ntripProfile['userName'] }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="input_{{$key}}_password">
                                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('password') }}</span>
                                                        </label>
                                                        <input type="text" class="form-control mt-n3" name="profiles[{{$key}}][password]" id="input_{{$key}}_password" value="{{ $ntripProfile['password'] }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(!$loop->last)
                                                <hr>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group row mb-0">
                            <div class="col-md-12 right">
                                <button type="submit" class="btn btn-success float-sm-right">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal HTML -->
<div id="deleteModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNetworkModalLabel">{{ __('Delete NTRIP profile') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{!! __('Are you sure you want to delete :name', ['name' => '<span id="js-deleteModal-name">NAME</span>']) !!}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                <a href="#" id="js-deleteModal-delete-a">
                    <button id="js-deleteModal-delete-btn" type="button" class="btn btn-danger">{{ __('Delete') }}</button>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addNtripModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('ntrip.add') }}" id="formAddNtrip">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addNetworkModalLabel">{{ __('Add NTRIP profile') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row col-lg-12">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="input_name">
                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('name') }}</span>
                                        </label>
                                        <input type="text" class="form-control mt-n3" name="name" id="input_name" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="input_host">
                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('host') }}</span>
                                        </label>
                                        <input type="text" class="form-control mt-n3" name="host" id="input_host" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="input_port">
                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('port') }}</span>
                                        </label>
                                        <input type="text" class="form-control mt-n3" name="port" id="input_port" value="2101" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="input_mountpoint">
                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('mountpoint') }}</span>
                                        </label>
                                        <input type="text" class="form-control mt-n3" name="mountpoint" id="input_mountpoint" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="input_username">
                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('username') }}</span>
                                        </label>
                                        <input type="text" class="form-control mt-n3" name="username" id="input_username" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="input_password">
                                            <span class="h6 small bg-white text-muted pt-1 pl-2 pr-2">{{ __('password') }}</span>
                                        </label>
                                        <input type="text" class="form-control mt-n3" name="password" id="input_password" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button class="btn btn-success" type="submit" id="btn_add">{{ __('Add') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function($) {
        $('#formAddNtrip').on('submit', function(){
            $('#btn_add').prop("disabled", true);
            $('#btn_add').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('Add') }}');
        });

        $(".js-delete-ntrip").click(function () {
            $('#js-deleteModal-name').text($(this).data('name'));
            $('#js-deleteModal-delete-a').attr('href', $(this).data('delete-url'));
            $('#deleteModal').modal('show');
        });

        $('#js-deleteModal-delete-btn').on('click', function(){
            $('#js-deleteModal-delete-btn').prop("disabled", true);
            $('#js-deleteModal-delete-btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('Delete') }}');
        });

    })(jQuery);

</script>
@endsection
