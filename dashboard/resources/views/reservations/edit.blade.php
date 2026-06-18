@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Forms</a></li>
            <li class="breadcrumb-item active" aria-current="page">Update Your Annonce</li>
        </ol>
    </nav>

    <div class="row">
        <div class="w-75 m-auto grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-5">Update Your Annonce</h4>

                    <div class="w-100">
                        @if(session('error'))
                            <div class="alert alert-danger fade show" role="alert">
                                <p class="text-center">
                                    {{session('error')}}
                                </p>
                            </div>
                        @endif
                    </div>
                    <form action="{{ route('annonces.update', $id) }}" method="POST" class="forms-sample">
                        @csrf
                        @method('PUT')
                        <input type="number" hidden="hidden" class="form-control" name="gp_id" id="exampleInputEmail1" placeholder="gp">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="exampleInputUsername1" class="form-label">Origine</label>
                                    <input type="text" class="form-control" id="exampleInputUsername1" value="{{$annonce['origin']}}" name="origin" autocomplete="off" placeholder="Origine">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Date Départ</label>
                                    <input type="datetime-local" class="form-control" id="exampleInputEmail1" value="{{$annonce['date_depart']}}" name="date_depart" placeholder="Date Départ">
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="exampleInputUsername1" class="form-label">Destination</label>
                                    <input type="text" class="form-control" id="exampleInputUsername1" autocomplete="off" value="{{$annonce['destination']}}" name="destination" placeholder="Destination">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Date Arrivée</label>
                                    <input type="datetime-local" class="form-control" id="exampleInputEmail1" value="{{$annonce['date_arrivee']}}" name="date_arrivee" placeholder="Date Arrivée">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="exampleInputUsername1" class="form-label">Prix/kg</label>
                                    <input type="number" min="0" step="0.1" class="form-control" id="exampleInputUsername1" autocomplete="off" value="{{$annonce['prix_du_kilo']}}" name="prix_du_kilo" placeholder="Destination">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Prix/piece</label>
                                    <input type="number" min="0" step="0.1" class="form-control" id="exampleInputEmail1" name="prix_par_piece" value="{{$annonce['prix_par_piece']}}" placeholder="Date Arrivée">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Poids Disponible</label>
                                    <input type="number" min="0" step="0.1" class="form-control" id="exampleInputEmail1" name="kilos_disponibles" value="{{$annonce['kilos_disponibles']}}" placeholder="Date Arrivée">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" name="description" rows="5">{{$annonce['description']}}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                        <button class="btn btn-secondary">Cancel</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/bootstrap-maxlength.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
@endpush
