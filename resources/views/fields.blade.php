@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Fields') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
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
                                                                <a href="{{ $field['downloadUrl'] }}"
                                                                   class="btn btn-primary">{{ __('Download') }}</a>
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
@endsection
