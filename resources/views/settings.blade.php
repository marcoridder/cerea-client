@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Settings') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form method="post" action="{{ route('settings.save') }}">
                        @csrf
                        @foreach($configItems as $configItem)
                            <div class="form-group row">
                                <label for="{{ $configItem['key'] }}" class="col-md-4 col-form-label text-md-right">{{ $configItem['name'] }}</label>

                                <div class="col-md-6">
                                    <input
                                        id="{{ $configItem['key'] }}"
                                        type="text"
                                        class="form-control @error($configItem['key']) is-invalid @enderror"
                                        name="{{ $configItem['key'] }}"
                                        value="{{ old($configItem['key']) ?? ($config->get($configItem['key']) ? : $configItem['default'] ?? null) }}"
                                        required
                                    >
                                    @error($configItem['key'])
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group row">
                            <label for="locale" class="col-md-4 col-form-label text-md-right">{{ __('Language') }}</label>

                            <div class="col-md-6">
                                <select
                                    name="locale"
                                    class="form-control"
                                >
                                    @foreach ($languages as $lang => $language)
                                            <option value="{{ $lang }}" @if($lang === $activeLanguage) selected @endif>
                                                {{$language}}
                                            </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4 right">
                                <button type="submit" class="btn btn-primary float-sm-right">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    <h4>{{ __('System Settings') }}:</h4>
                    <div class="row py-2 align-items-center">
                        <div class="col-sm-4">
                            <span class="col-sm-3">{{ __(":appName version :version", ['appName' => config('app.name'), 'version' => $cereaClientVersion]) }}</span>
                        </div>
                        <div class="col-sm-8">
                            <button id="js-checkUpdateBtn" class="btn btn-primary" type="button">{{ __('Check for update') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="updateModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Update') }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <a href="{{ route('update.update') }}">
                    <button id="js-updateBtn" type="button" class="btn btn-success" hidden>{{ __('Update') }}</button>
                </a>
                <button id="js-updateCancelBtn" type="button" class="btn btn-primary" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        $('#js-checkUpdateBtn').on('click', function(){
            checkUpdate();
        });

        $('#js-updateBtn').on('click', function(){
                $('#js-updateBtn').prop("disabled", true);
                $('#js-updateCancelBtn').prop("disabled", true);
                $('#js-updateBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('Update') }}');
        });
    })(jQuery);

    function checkUpdate() {
        var updateModal = $('#updateModal');
        updateModal.find('#modal-body').html('<div class="spinner-border" role="status"><span class="sr-only">{{ __('Loading...') }}</span></div>');
        updateModal.modal();

        $.ajax({
            type: "GET",
            url: '{{ route('api.checkUpdate') }}',
            dataType: 'json',
            success: function(data){
                if (data.updateable === true) {
                    updateModal.find('#js-updateBtn').removeAttr('hidden');
                }
                htmlData = data.message;
                updateModal.find('.modal-body').html(htmlData);
            },
            error: function(){
                updateModal.find('.modal-body').html('{{ __('Error searching for new version') }}');
            }
        });

        return false;
    }

</script>

@endsection
