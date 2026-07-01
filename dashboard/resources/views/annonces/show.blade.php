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
            <li class="breadcrumb-item active" aria-current="page">Details Annonce</li>
        </ol>
    </nav>
    <div class="row">
        <div class="w-75 m-auto grid-margin stretch-card">
            <div class="card">
                @if(Session::has("error"))
                    <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                        <strong>{{ Session::get("error") }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                    </div>
                @endif
                <div class="card-body">
                    <h4 class="card-title">Details Annonce</h4>
                    <div class="d-flex align-items-center mb-5">
                        <img src="#" class="border border-primary p-4 rounded-circle me-3"
                             style="width:50px;height:50px;object-fit:cover" alt="GP"/>
                        <div>
                            <h4 class="mb-0">{{$annonce['gp']['email']}}</h4>
                            <small class="text-muted">{{$annonce['description']}}</small>
                        </div>
                    </div>
                    <!-- <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                    </svg> -->
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Départ</label>
                                <input class="form-control border-primary" readonly
                                       value="{{$annonce['origin']}}"/>
                            </div>
                    

                            <div class="col-md-6">
                                <label class="form-label">Date Départ</label>
                                <input class="form-control border-primary" readonly
                                       value="{{\Carbon\Carbon::parse($annonce['date_arrivee'])->format('d/m/Y à H:i')}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Arrivée</label>
                                <input class="form-control border-primary" readonly
                                       value="{{$annonce['destination']}}"/>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date Arrivée</label>
                                <input  class="form-control border-primary" readonly
                                       value="{{\Carbon\Carbon::parse($annonce['date_arrivee'])->format('d/m/Y à H:i')}}"/>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Prix / Kg</label>
                                <input class="form-control border-primary" readonly
                                       value="{{$annonce['prix_du_kilo'] ?? $annonce['prix_par_kilo'] ?? '—'}}"/>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prix / Pièce</label>
                                <input class="form-control border-primary " readonly
                                       value="{{$annonce['prix_par_piece'] ?? '—'}}"/>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Poids disponibles</label>
                                <input class="form-control border-primary " readonly
                                       value="{{$annonce['kilos_disponibles'] ?? 0}}"/>
                            </div>
                        </div>
                    </div>

                    <hr/>
                    <input type="hidden" id="prix-kilo"
                           value="{{ $annonce['prix_du_kilo'] ?? $annonce['prix_par_kilo'] ?? 0 }}">

                    <input type="hidden" id="prix-piece"
                           value="{{ $annonce['prix_par_piece'] ?? 0 }}">
                    <form action="{{ route('reservations.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="annonce_id" value="{{$id}}" id="annonce_id">

                        <h5 class="mb-3">Marchandises au kilo</h5>

                        <div id="kilo-items">
                            <div class="row mb-3 kilo-item">
                                <div class="col-md-6">
                                    <label>Désignation</label>
                                    <input type="text"
                                           name="kilos[0][item_name]"
                                           class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Poids (Kg)</label>
                                    <input type="number"
                                           step="0.1"
                                           min="0"
                                           name="kilos[0][weight]"
                                           class="form-control">
                                </div>

                                {{--<div class="col-md-2">
                                    <button type="button"
                                            class="btn btn-danger remove-kilo">
                                        Supprimer
                                    </button>
                                </div>--}}
                            </div>

                        </div>

                        <button type="button"
                                id="add-kilo"
                                class="btn btn-outline-primary mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg> Item au kilo
                        </button>

                        <hr>

                        <h5 class="mb-3">Marchandises spéciales</h5>

                        <div id="special-items">

                            <div class="row mb-3 special-item">
                                <div class="col-md-6">
                                    <label>Désignation</label>
                                    <input type="text"
                                           name="specials[0][item_name]"
                                           class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label>Quantité</label>
                                    <input type="number"
                                           min="1"
                                           name="specials[0][quantity]"
                                           class="form-control">
                                </div>

                                {{--<div class="col-md-2">
                                    <button type="button"
                                            class="btn btn-danger remove-special">
                                        Supprimer
                                    </button>
                                </div>--}}
                            </div>

                        </div>

                        <button type="button"
                                id="add-special"
                                class="btn btn-outline-primary mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg> Item spécial
                        </button>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <h4>Estimation totale :</h4>
                            <h4 id="total-estime">0
                                <span class="ms-2 text-muted small">FCFA</span>
                            </h4>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Réserver
                        </button>
                    </form>

                    {{--<form  action="{{ route('annonces.update',$annonce->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">GP</label>
                            <input id="name" class="form-control @if($errors->any()) is-invalid @endif" name="title" value="Title" type="text">
                            @if($errors->any())
                                <div class="invalid-feedback">
                                    title is required
                                </div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input id="image" required class="form-control @if($errors->any()) is-invalid @endif" name="image" value="Image" type="file">
                            @if($errors->any())
                                <div class="invalid-feedback">
                                    Image is required
                                </div>
                            @endif
                        </div>
                        <div id="ingredients-container" class="mb-3">
                            <h4>Ingrédients</h4>
                            <div class="ingredient-item">
                                @foreach([3, 4] as $ingredient)
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control @error('ingredients[0][name]') is-invalid @enderror" name="ingredients[0][name]" value="test" placeholder="Nom de l'ingrédient" required>
                                            @if('ingredients[0][name]')
                                                <div class="invalid-feedback">
                                                    name ingredient required
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control @error('ingredients[0][quantity]') is-invalid @enderror" name="ingredients[0][quantity]" value="test" placeholder="Quantity" required>
                                            @if('ingredients[0][quantity]')
                                                <div class="invalid-feedback">
                                                    quantity ingredient required
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control @error('ingredients[0][metric]') is-invalid @enderror" name="ingredients[0][metric]" value="test" placeholder="Metric" required>
                                            @if('ingredients[0][metric]')
                                                <div class="invalid-feedback">
                                                    metric ingredient required
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control @error('ingredients[0][calories]') is-invalid @enderror" name="ingredients[0][calories]" value="test" placeholder="Calories" required>
                                            @if('ingredients[0][calories]')
                                                <div class="invalid-feedback">
                                                    calories ingredient required
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-secondary" id="add-ingredient"><i data-feather="plus"></i>Ajouter un ingrédient</button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <div>

                                @foreach([1, 3] as $category)
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" name="categories[]" class="form-check-input @if($errors->any()) is-invalid @endif" @if(1==2) checked @endif id="checkInline{{$annonce->id}}" value="test">
                                        @if($errors->any())
                                            <div class="invalid-feedback">
                                                categories is required
                                            </div>
                                        @endif
                                        <label class="form-check-label" for="checkInline{{$annonce->id}}">
                                            test
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="nutrition" class="form-label">Nutrition</label>
                            <input class="form-control @error("nutrition") is-invalid @enderror" id="nutrition" name="nutrition" rows="5" value="test" />
                            @error("nutrition")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="prepTime" class="form-label">Prepare Time</label>
                            <input class="form-control @error("prepTime") is-invalid @enderror" id="prepTime" name="prepTime" rows="5" value="test" />
                            @error("prepTime")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="cookTime" class="form-label">Cooking Time</label>
                            <input class="form-control @error("cookTime") is-invalid @enderror" id="cookTime" name="cookTime" rows="5" value="test" />
                            @error("cookTime")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="video" class="form-label">Url Video</label>
                            <input class="form-control @error("video") is-invalid @enderror" id="video" name="video" rows="5" value="test" />
                            @error("video")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="summary" class="form-label">Summary</label>
                            <textarea class="form-control @error("summary") is-invalid @enderror" id="summary" name="summary" rows="5">test</textarea>
                            @error("summary")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Instructions</label>
                            <textarea class="form-control @error("instructions") is-invalid @enderror" id="instructions" name="instructions" rows="5">test</textarea>
                            @error("instructions")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </form>--}}
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


