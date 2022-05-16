@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('Fields') }}
                        <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#uploadFieldModal">
                            {{ __('Upload field') }}
                        </button>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <div class="container-fluid content-row">
                            @foreach($fields as $clientName => $clientFields)
                                <div class="col-md-12 mb-4">
                                    <div class="card">
                                        <div class="card-header">{{ $clientName }}</div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($clientFields as $field)
                                                    <div class="col-12 col-lg-4 col-md-6 mb-4">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                {{ $field['name'] }}

                                                                @if($field['downloadable'])
                                                                    <a href="{{ $field['downloadUrl'] }}" class="btn btn-primary float-right">
                                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                            <div class="card-body">
                                                                <h5 class="card-title">{{ __('Patterns') }}</h5>
                                                                <p class="card-text">
                                                                    <ul>
                                                                        @foreach($field['patterns'] as $pattern)
                                                                            <li>{{ $pattern}}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </p>
                                                                {{--                                            <a href="{{ $field['editUrl'] }}" class="btn btn-primary">Bewerken</a>--}}
                                                            </div>
                                                            <div class="card-footer text-muted">
                                                                {{--                                        2 days ago--}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadFieldModal" tabindex="-1" role="dialog" aria-labelledby="uploadFieldModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" enctype="multipart/form-data" action="{{ route('fields.upload') }}" id="formUploadField">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Upload field') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">

                                <div class="alert alert-warning">
                                    {{ __('Contours and patterns will be overwritten!') }}
                                </div>

                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" id="file" required>
                                    <label class="custom-file-label" for="file">{{ __('Choose field file') }}...</label>
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
        $(document).ready(function () {
            bsCustomFileInput.init()
        })
    </script>
@endsection
