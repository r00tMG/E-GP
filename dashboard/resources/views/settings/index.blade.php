@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush

@section('content')
    {{-- @dump($meals)--}}
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Settings</li>
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
                    <h4>Version</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"></li>
                        <li class="list-group-item">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon2">version-koool</span>
                                <form method="post" action="{{ route(isset($setting) && $setting->exists ? 'settings.update' : 'settings.store',$setting ?? null)}}">
                                    @csrf
                                    @method(isset($setting) && $setting->exists ? 'PUT' : 'POST')
                                    <input type="text" name="version" value="{{isset($setting) && $setting->exists ? $setting->version : ''}}" placeholder="Saisir la version" class="form-control" id="basic-url" aria-describedby="basic-addon3">
                                </form>
                            </div>
                        </li>
                        <li class="list-group-item"></li>
                    </ul>
                    <div class="d-flex justify-content-between align-items">
                        <h4 class="mt-1">Mode Maintenance</h4>
                        <form action="{{ route('maintenance') }}" method="POST">
                            @csrf
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" class="form-check-input" id="formSwitch1" name="maintenance_mode" onchange="this.form.submit()"
                                       @if(App::isDownForMaintenance()) checked @endif>
                                <label class="form-check-label" for="formSwitch1">
                                    {{ App::isDownForMaintenance() ? 'Désactiver le mode maintenance' : 'Activer le mode maintenance' }}
                                </label>
                            </div>
                        </form>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"></li>
                        <h4 class="my-3">API Keys</h4>
{{--                        @dump($errors->key)--}}
                        <li class="list-group-item">
                            <form class="mt-2" method="POST" action="{{ route( isset($apikey) && $apikey->exists ? 'apikeys.update' :  'apikeys.store',$apikey ?? null ) }}">
                                @csrf
                                @method(isset($apikey) && $apikey->exists ? 'PUT' : 'POST')
                                <div class="row m-auto " >
                                    <div class="input-group">
                                        <div class="col-md-4">
                                            <input class="form-control" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" required pattern="^[A-Za-z0-9_]+$" @if(isset($apikey) && $apikey->exists) value="{{$apikey->key}}" @endif  name="key" placeholder="Key">
                                            <span class="invalid-feedback">Veuillez entrer un format valide (XXXX_YYYY_ZZZZ).</span>
                                        </div>
                                        <div class="col-md-6 me-2">
                                            <input class="form-control" required  name="value" @if(isset($apikey) && $apikey->exists) value="{{$apikey->key}}" @endif placeholder="Value">
                                        </div>
                                        <div class="col-md-1">
                                        <button class="btn btn-primary" type="submit">
                                            @if(isset($apikey) && $apikey->exists)
                                                Update
                                            @else
                                                Save
                                            @endif
                                        </button>
                                    </div>
                                    </div>
                                </div>
                            </form>
                        </li>
                        <li class="list-group-item">

                            <div class="container">
                                <table class="table rounded-1 table-responsive">
                                    <thead>
                                    <tr>
                                        <th class="col-md-4">Key</th>
                                        <th class="col-md-6">Value</th>
                                        <th class="col-md-2" colspan="2">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($apiKeys as $apikey)
                                            <tr>
                                                <td class="col-md-4">{{$apikey->key}}</td>
                                                <td class="col-md-6">{{$apikey->value}}</td>
                                                <td class="">
                                                    <form method="POST" action="{{route('apikeys.destroy',$apikey->id)}}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger circle"><i data-feather="delete"></i></button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm btn-success circle" href="{{ route('apikeys.edit',$apikey->id) }}" ><i data-feather="edit"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

