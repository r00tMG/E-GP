@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/pickr/themes/classic.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
     <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Les utilisateurs</a></li>
          <li class="breadcrumb-item active" aria-current="page">Nouveau utilisateur</li>
     </ol>
</nav>

<div class="row">
     <div class="w-75 m-auto grid-margin stretch-card">
          <div class="card">
               <div class="card-body">
                    <h4 class="card-title">Ajouter un nouveau utilisateur</h4>

                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                         @csrf

                         <div class="form-group mb-3">
                              <label for="name" class="form-label">Name</label>
                              <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Name">
                              @error('name')
                                   <div class="invalid-feedback">
                                        {{ $message }}
                                   </div>
                              @enderror
                         </div>
                         <div class="form-group mb-3">
                              <label for="email" class="form-label">Email</label>
                              <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email">
                              @error('email')
                                   <div class="invalid-feedback">
                                        {{ $message }}
                                   </div>
                              @enderror
                         </div>
                         <div class="form-group mb-3">
                              <label for="password" class="form-label">Mot de passe</label>
                              <input type="password" class="form-control @error('email') is-invalid @enderror" id="password" name="password" placeholder="Mot de passe" />
                              @error("password")
                                   <div class="invalid-feedback">
                                        {{ $message }}
                                   </div>
                              @enderror
                         </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Confirmer votre mot de passe</label>
                            <input type="password" class="form-control @error('email') is-invalid @enderror" id="password" name="confirm-password" placeholder="Confirmer votre Mot de passe" />
                            @error("password")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="roles[]" class="form-select" multiple>
                                @foreach($roles as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error("role")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                         <button class="btn btn-primary" type="submit">Create</button>
                    </form>
               </div>
          </div>
     </div>

</div>
@endsection

@push('plugin-scripts')
     <script src="{{ asset('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
@endpush

@push('custom-scripts')
     <script src="{{ asset('assets/js/bootstrap-maxlength.js') }}"></script>
     <script src="{{ asset('assets/js/script.js') }}"></script>
@endpush
