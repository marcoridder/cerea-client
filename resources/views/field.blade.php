@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Perceel {{ $field['name'] }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="post" action="{{ route('field.save') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="patterns" class="col-md-4 col-form-label text-md-right">Lijnen</label>

                            <div class="col-md-12">
                                <textarea id="patterns" rows="20" type="text" class="form-control @error('patterns') is-invalid @enderror" name="patterns" required>{{ old('patterns') ?? $patterns }}</textarea>
                                @error('patterns')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    Opslaan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
