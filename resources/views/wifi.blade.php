@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Wifi
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
                            Verbonden met {{$connectedWifiNetwork}}
                        </div>
                    @elseif(count($savedWifiNetworks))
                        <div class="alert alert-danger">
                            Geen wifi netwerk verbonden
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            Wifi netwerken
                            <button type="button" class="btn btn-primary float-sm-right" id="addNetworkBtn">
                                Wifi netwerk toevoegen
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
                <h5 class="modal-title" id="addNetworkModalLabel">Wifi netwerk verwijderen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Weet je zeker dat je <span id="js-deleteModal-ssid">SSID</span> wilt verwijderen?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                <a href="#" id="js-deleteModal-delete-a">
                    <button id="js-deleteModal-delete-btn" type="button" class="btn btn-danger">Delete</button>
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
                    <h5 class="modal-title" id="addNetworkModalLabel">Wifi netwerk toevoegen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <button type="button" class="btn btn-primary" id="addNetworkRefresh">
                                <i class="fas fa-sync-alt"></i>
                                Vernieuwen
                            </button>
                        </div>
                        <div class="row py-3">
                            <div class="" id="modal-body"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
                    <button class="btn btn-success" type="submit" id="btn_add">Toevoegen</button>
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
            $('#btn_add').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Toevoegen');
        });

        $(".js-delete-wifi").click(function () {
            $('#js-deleteModal-ssid').text($(this).data('ssid'));
            $('#js-deleteModal-delete-a').attr('href', $(this).data('delete-url'));
            $('#deleteModal').modal('show');
        });

        $('#js-deleteModal-delete-btn').on('click', function(){
            $('#js-deleteModal-delete-btn').prop("disabled", true);
            $('#js-deleteModal-delete-btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Delete');
        });

    })(jQuery);

    function getWifiNetworks() {
        $('#addNetworkRefresh').prop("disabled", true);
        var addNetworkModal = $('#addNetwork');
        addNetworkModal.find('#modal-body').html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
        addNetworkModal.modal();

        $.ajax({
            type: "GET",
            url: '{{ route('api.wifi') }}',
            dataType: 'json',
            success: function(data){
                htmlData = '<fieldset class="form-group">' +
                    '<div class="">';
                $.each(data, function(i, ssid) {
                    htmlData += '<div class="form-check">' +
                        '<input class="form-check-input" type="radio" name="ssid" id="'+ssid+'" value="'+ssid+'">' +
                        '<label class="form-check-label" for="'+ssid+'">'+ssid+'</label>' +
                        '</div>';
                });
                htmlData += '</div></fieldset>' +
                    '<input type="text" name="password" placeholder="Wachtwoord" required>';

                addNetworkModal.find('#modal-body').html(htmlData);
                $('#addNetworkRefresh').prop("disabled", false);
            }
        });

        return false;
    }

</script>
@endsection
