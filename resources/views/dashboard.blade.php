@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('Dashboard') }}
                    <a href="{{ route('dashboard.index') }}" class="btn btn-primary float-sm-right">
                        {{ __('Refresh') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    {{ __('Network') }}
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            @if($hasInternet)
                                                <div class="alert alert-success">
                                                    {{ __('Connected to the internet') }}
                                                </div>
                                            @else
                                                <div class="alert alert-danger">
                                                    {{ __('No internet connection') }}!
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-12">
                                            @if($hasWifiNetworks)
                                                @if($connectedWifiNetwork)
                                                    <div class="alert alert-success">
                                                        {{ __('Connected with :ssid', ['ssid' => $connectedWifiNetwork]) }}
                                                    </div>
                                                @else
                                                    <div class="alert alert-danger">
                                                        {{ __('No WiFi connection') }}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    ECU
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div id="js-cpu-temperature-class" class="alert alert-{{ $systemData['cpuTemperatureClass'] ?? 'success' }}">
                                                CPU: <span id="js-cpu-temperature">{{ $systemData['cpuTemperature'] ?? null }}</span> &#8451;
                                            </div>
                                        </div>
                                    </div>
{{--                                    <div class="row py-2 align-items-center">--}}
{{--                                        <div class="col-sm-4">--}}
{{--                                            <span class="col-sm-3">Power:</span>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-sm-8">--}}
{{--                                            <button id="reboot-button" class="btn btn-primary" type="button">Reboot</button>--}}
{{--                                            <button id="shutdown-button" class="btn btn-danger" type="button">Shutdown</button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
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
    function getSystemData() {
        $.ajax({
            type: "GET",
            url: '{{ route('api.systemdata') }}',
            dataType: 'json',
            success: function(data){
                $('#js-cpu-temperature').text(data.cpuTemperature);
                $('#js-cpu-temperature-class').removeClass();
                $('#js-cpu-temperature-class').addClass('alert alert-'+data.cpuTemperatureClass);
            }
        });
    }
    setInterval(getSystemData, 5000);
</script>
@endsection
