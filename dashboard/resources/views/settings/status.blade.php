@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush

@section('content')
    {{-- @dump($meals)--}}
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Status</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12">
            @if(Session::has("success"))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>{{ Session::get("success") }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                </div>
            @endif
            @if(Session::has("error"))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>{{ Session::get("erreur") }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                </div>
            @endif
        </div>
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4>Status</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"></li>
                        <li class="list-group-item">
                            <div class=" d-flex justify-content-between align-items">
                                <span class="fs-5">Base de données</span>
                                <span class="badge @if(isset($services) && $services['database'] == 'Operational') bg-primary @else bg-danger @endif">{{$services['database']}}</span>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class=" d-flex justify-content-between align-items">
                                <span class="fs-5">Cache</span>
                                <p class="badge @if(isset($services) && $services['cache'] == 'Operational') bg-primary @else bg-danger @endif">{{$services['cache']}}</p>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class=" d-flex justify-content-between align-items">
                                <span class="fs-5">Api</span>
                                <span class="badge @if(isset($services) && $services['api'] == 'Operational') bg-primary @else bg-danger @endif">{{$services['api']}}</span>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class=" d-flex justify-content-between align-items">
                                <span class="fs-5">Server</span>
                                <span class="badge @if(isset($services) && $services['server'] == 'Operational') bg-primary @else bg-danger @endif">{{$services['server']}}</span>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/data-table.js') }}"></script>
    <script src="{{ asset('assets/js/script1.js') }}"></script>
@endpush

