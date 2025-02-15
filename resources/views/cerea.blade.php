@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Cerea
                </div>

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
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    {{ __('Cerea Android app') }}
                                    <div class="alert alert-info">
                                        <i class="fas fa-info"></i> {{ __('The tablet needs internet for a Cerea update.') }}
                                    </div>
                                </div>

                                <div class="card-body">
                                    @if($cereaVersions)
                                        <ul class="list-group list-group-flush">
                                            @foreach($cereaVersions as $cereaVersion)
                                                <li class="list-group-item">
                                                    {{ $cereaVersion['version'] }} ({{ $cereaVersion['size'] }})
                                                    <a href="{{ route('cerea.app-download', $cereaVersion['version']) }}" class="js-download-backup-a">
                                                        <button type="button" class="btn btn-primary js-download-backup-btn">{{ __('Download') }}</button>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        {{ __('No versions found') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    {{ __('Cerea ECU') }}
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-warning" id="js-restart-btn"
                                                onclick="if(confirm('{{ __('Reboot Cerea ECU') }}?')){reboot();}"
                                            >
                                                {{ __('Reboot Cerea ECU') }}
                                            </button>
                                        </div>
                                        <div class="col-lg-6">
                                            <button type="button" class="btn btn-danger" id="js-off-btn"
                                                onclick="if(confirm('{{ __('Shutdown Cerea ECU') }}?')){off();}"
                                            >
                                                {{ __('Shutdown Cerea ECU') }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-lg-12">
                                            <div id="js-status" class="alert alert-warning d-none">
                                                <span id="js-status-spinner" class="spinner-border spinner-border-sm d-none"></span>
                                                <span id="js-status-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    {{ __('Cerea') }}
                                </div>
                                <div class="card-body">
                                    <a href="{{ route('cerea.backup') }}" class="js-download-backup-a">
                                        <button type="button" class="btn btn-primary js-download-backup-btn">{{ __('Download Cerea backup') }}</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {

        $('.js-download-backup-a').on('click', function(){
            btn = $(this).find('.js-download-backup-btn');
            btn.prop("disabled", true);
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> '+btn.html());
        });

    })(jQuery);

    function off() {
        document.getElementById("js-off-btn").disabled = true;
        document.getElementById("js-restart-btn").disabled = true;
        // Creating Our XMLHttpRequest object
        let xhr = new XMLHttpRequest();

        // Making our connection
        let url = '{{ route('system.off') }}';
        xhr.open("GET", url, true);

        // function execute after request is successful
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("js-status").classList.remove('d-none');
                document.getElementById("js-status").classList.remove('alert-warning');
                document.getElementById("js-status").classList.add('alert-success');
                document.getElementById("js-status-text").innerText = "{{ __('ECU showdown in progress...') }}";
            }
        }
        // Sending our request
        xhr.send();
    }

    function reboot() {
        const restartUrl = "{{ route('system.reboot') }}";

        document.getElementById("js-status-spinner").classList.remove("d-none");
        document.getElementById("js-status").classList.remove('alert-success');
        document.getElementById("js-status").classList.add('alert-warning');
        document.getElementById("js-restart-btn").disabled = true;
        document.getElementById("js-off-btn").disabled = true;
        document.getElementById("js-status").classList.remove('d-none');
        document.getElementById("js-status-text").innerText = "{{ __('ECU reboot in progress...') }}";

        fetch(restartUrl, { method: "GET" })
            .then(() => {
                checkIfOnline();
            })
            .catch(() => {
                checkIfOnline();
            });
    }

    function checkIfOnline() {
        const checkUrl = "{{ route('dashboard.index') }}";

        let interval = setInterval(() => {
            fetch(checkUrl, { method: "GET", cache: "no-cache" })
                .then(response => {
                    if (response.ok) {
                        clearInterval(interval);
                        document.getElementById("js-status-spinner").classList.add("d-none");
                        document.getElementById("js-status").classList.remove('alert-warning');
                        document.getElementById("js-status").classList.add('alert-success');
                        document.getElementById("js-status-text").innerText = "{{ __('ECU is online!') }}";
                        document.getElementById("js-restart-btn").disabled = false;
                        document.getElementById("js-off-btn").disabled = false;
                    }
                })
                .catch(() => {
                });
        }, 2000);
    }

</script>
@endsection
