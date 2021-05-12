@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">

        <div class="col-md-12" style="height: 600px;">
            <div class="card h-100">
                <div class="card-header">Logs</div>
                <iframe src="/log-viewer" id="frame" frameborder="0" style="width: 100%; height: 100%;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection
