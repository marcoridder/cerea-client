@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __('WiFi') }}
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

                    @if($connectedWifiNetwork)
                        <div class="alert alert-success">
                            {{ __('Connected to :ssid', ['ssid' => $connectedWifiNetwork]) }}
                        </div>
                    @elseif(count($savedWifiNetworks))
                        <div class="alert alert-danger">
                            {{ __('No WiFi network connected') }}
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            {{ __('WiFi networks') }}
                            <button type="button" class="btn btn-primary float-sm-right" id="addNetworkBtn">
                                {{ __('Add WiFi network') }}
                            </button>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach($savedWifiNetworks as $savedWifiNetwork)
                                    <li class="list-group-item">
                                        {{ $savedWifiNetwork['ssid'] }}
                                        <a
                                           href="#"
                                           class="js-delete-wifi"
                                           data-ssid="{{ $savedWifiNetwork['ssid'] }}"
                                           data-delete-url="{{ route('wifi.delete', $savedWifiNetwork['ssid']) }}"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
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
                <h5 class="modal-title" id="addNetworkModalLabel">{{ __('Delete WiFi network') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{!! __('Are you sure you want to delete :ssid', ['ssid' => '<span id="js-deleteModal-ssid">SSID</span>']) !!}</p>
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

<div class="modal fade" id="addNetwork" tabindex="-1" role="dialog" aria-labelledby="addNetworkModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('wifi.save') }}" id="formAddWifi">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addNetworkModalLabel">{{ __('Add WiFi network') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row col-lg-12">
                            <button type="button" class="btn btn-primary" id="addNetworkRefresh">
                                <i class="fas fa-sync-alt"></i>
                                {{ __('Refresh') }}
                            </button>
                        </div>
                        <div class="row py-3">
                            <div class="js-loader" hidden>
                                <div class="spinner-border" role="status"><span class="sr-only">{{ __('Loading...') }}.</span></div>
                            </div>

                            <div class="js-body" hidden>
                                <script type="text/plain" id="js-ssid-template">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input" type="radio" name="ssid" value="{ssid}" required>
                                            {ssid}
                                        </label>
                                    </div>
                                </script>
                                <div class="form-group col-lg-12" id="js-ssid-content"></div>
                                <div class="form-group col-lg-12">
                                    <label for="password">{{ __('Password') }}</label>
                                    <input type="text" name="password" class="form-control " required>
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
        $('#addNetworkBtn').on('click', function(){
            getWifiNetworks();
        });

        $('#addNetworkRefresh').on('click', function(){
            getWifiNetworks();
        });

        $('#formAddWifi').on('submit', function(){
            $('#btn_add').prop("disabled", true);
            $('#btn_add').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('Add') }}');
        });

        $(".js-delete-wifi").click(function () {
            $('#js-deleteModal-ssid').text($(this).data('ssid'));
            $('#js-deleteModal-delete-a').attr('href', $(this).data('delete-url'));
            $('#deleteModal').modal('show');
        });

        $('#js-deleteModal-delete-btn').on('click', function(){
            $('#js-deleteModal-delete-btn').prop("disabled", true);
            $('#js-deleteModal-delete-btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('Delete') }}');
        });

    })(jQuery);

    function getWifiNetworks() {
        $('#addNetworkRefresh').prop("disabled", true);
        var addNetworkModal = $('#addNetwork');
        var loader = addNetworkModal.find('.js-loader');
        var body = addNetworkModal.find('.js-body');
        loader.removeAttr('hidden');
        body.attr('hidden', true);

        addNetworkModal.modal();

        $.ajax({
            type: "GET",
            url: '{{ route('api.wifi') }}',
            dataType: 'json',
            success: function(data){
                var template = $("#js-ssid-template");
                htmlData = '';
                $.each(data, function(i, ssid) {
                    templateHtml = template.html();

                    templateHtml = templateHtml.replaceAll('{ssid}', ssid);
                    htmlData += templateHtml;
                });

                $("#js-ssid-content").empty();
                addNetworkModal.find('#js-ssid-content').html(htmlData);

                loader.attr('hidden', true);
                body.removeAttr('hidden');

                $('#addNetworkRefresh').prop("disabled", false);
            }
        });

        return false;
    }

</script>
@endsection
