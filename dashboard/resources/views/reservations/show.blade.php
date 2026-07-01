@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/plugins/pickr/themes/classic.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet"/>
@endpush

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Forms</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details Reservation</li>
        </ol>
    </nav>
    <div class="row">
        <div class="w-75 m-auto grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Details Reservation</h4>
                    <div class="d-flex align-items-center mb-5">
                        <img src="#" class="border border-primary p-4 rounded-circle me-3"
                             style="width:50px;height:50px;object-fit:cover" alt="GP"/>
                        <div>
                            <h4 class="mb-0">{{$reservation["annonce"]['gp']['email']}}</h4>
                            <small class="text-muted">{{$reservation["annonce"]['description']}}</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Départ</label>
                                <input class="form-control border-primary" readonly
                                       value="{{$reservation["annonce"]['origin']}}"/>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date Départ</label>
                                <input class="form-control border-primary" readonly
                                       value="{{\Carbon\Carbon::parse($reservation["annonce"]['date_arrivee'])->format('d/m/Y à H:i')}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Arrivée</label>
                                <input class="form-control border-primary" readonly
                                       value="{{$reservation["annonce"]['destination']}}"/>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date Arrivée</label>
                                <input class="form-control border-primary" readonly
                                       value="{{\Carbon\Carbon::parse($reservation["annonce"]['date_arrivee'])->format('d/m/Y à H:i')}}"/>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Prix (kg)</label>
                                <input class="form-control border-primary" readonly
                                       value="{{$reservation["annonce"]['prix_du_kilo'] ?? $reservation["annonce"]['prix_par_kilo'] ?? '—'}}"/>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prix (Pièce)</label>
                                <input class="form-control border-primary " readonly
                                       value="{{$reservation["annonce"]['prix_par_piece'] ?? '—'}}"/>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Poids disponibles</label>
                                <input class="form-control border-primary " readonly
                                       value="{{$reservation["annonce"]['kilos_disponibles'] ?? 0}}"/>
                            </div>
                        </div>
                    </div>

                    <hr/>

                        <h5 class="mb-3">Marchandises au kilo</h5>

                        <div id="kilo-items">
                            <div class="row mb-3 kilo-item">
                                <div class="col-md-6">
                                    <label>Désignation</label>
                                    @foreach($reservation['items'] as $item)
                                        <input type="text"
                                               readonly
                                               value="{{$item["item_name"]}}"
                                               class="form-control">
                                    @endforeach
                                </div>

                                <div class="col-md-4">
                                    <label>Poids (Kg)</label>
                                    @foreach($reservation['items'] as $item)
                                        <input type="number"
                                               step="0.1"
                                               min="0"
                                               readonly
                                               value="{{$item["weight"]}}"
                                               class="form-control">
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">Marchandises spéciales</h5>

                        <div id="special-items">

                            <div class="row mb-3 special-item">
                                <div class="col-md-6">
                                    <label>Désignation</label>
                                    @foreach($reservation['special_items'] as $special_items)
                                    <input type="text"
                                           readonly
                                           value="{{$special_items['item_name']}}"
                                           class="form-control">
                                    @endforeach
                                </div>

                                <div class="col-md-4">
                                    <label>Quantité</label>

                                    @foreach($reservation['special_items'] as $special_items)
                                        <input type="number"
                                               min="1"
                                               readonly
                                               value="{{$special_items['quantity']}}"
                                               class="form-control">
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <h4>Prix Total :</h4>
                            <div class="d-flex justify-content-center align-items-baseline">
                                    <span class="fs-3 fw-bold">
                                        {{ number_format($reservation['total_price'], 0, ',', ' ') }}
                                    </span>
                                <span class="ms-2 text-muted small">FCFA</span>
                            </div>
                        </div>

                        <a href="{{route("pricing", $reservation['id'])}}" type="submit" class="btn btn-primary w-100">
                            Confirmer
                        </a>
                </div>
            </div>
        </div>
    </div>

@endsection



@push('plugin-scripts')
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/typeahead-js/typeahead.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/pickr/pickr.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
@endpush

@push('custom-scripts')
    <script src="{{ asset('assets/js/form-validation.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-maxlength.js') }}"></script>
    <script src="{{ asset('assets/js/inputmask.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <script src="{{ asset('assets/js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/js/tags-input.js') }}"></script>
    <script src="{{ asset('assets/js/dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/dropify.js') }}"></script>
    <script src="{{ asset('assets/js/pickr.js') }}"></script>
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>

@endpush


