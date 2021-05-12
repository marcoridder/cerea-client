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
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    Cerea app
                                </div>

                                <div class="card-body">
                                    @if($cereaVersions)
                                        <ul class="list-group list-group-flush">
                                            @foreach($cereaVersions as $cereaVersion)
                                                <li class="list-group-item">
                                                    {{ $cereaVersion['version'] }} ({{ $cereaVersion['size'] }})
                                                    <a href="{{ route('cerea.app-download', $cereaVersion['version']) }}" class="js-download-backup-a">
                                                        <button type="button" class="btn btn-primary js-download-backup-btn">Download</button>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        Geen versies gevonden
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('cerea.backup') }}" class="js-download-backup-a">
                                <button type="button" class="btn btn-primary js-download-backup-btn">Download Cerea backup</button>
                            </a>
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
</script>
@endsection
