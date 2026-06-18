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
          <li class="breadcrumb-item active" aria-current="page">Modifie utilisateur</li>
     </ol>
</nav>

<div class="row">
     <div class="w-75 m-auto grid-margin stretch-card">
          <div class="card">
               <div class="card-body">
                    <h4 class="card-title">Modifie utilisateur</h4>

                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                         @csrf
                         @method('PUT')

                         <div class="form-group mb-3">
                              <label for="name" class="form-label">Name</label>
                              <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Name" value="{{ $user->name }}">
                              @error('name')
                                   <div class="invalid-feedback">
                                        {{ $message }}
                                   </div>
                              @enderror
                         </div>
                         <div class="form-group mb-3">
                             <label for="email" class="form-label">Email</label>
                             <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ $user->email }}">
                             @error('email')
                             <div class="invalid-feedback">
                                 {{ $message }}
                             </div>
                             @enderror
                         </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Confirmer votre Mot de passe" />
                            @error("password")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Confirmer votre mot de passe</label>
                            <input type="password" class="form-control @error('email') is-invalid @enderror" id="confirm-password" name="confirm-password" placeholder="Confirmer votre Mot de passe" />
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
                                     <option value="{{ $key }}" @if(in_array($key, $userRole)) selected @endif>{{ $value }}</option>
                                 @endforeach
                             </select>
                              @error("roles")
                                   <div class="invalid-feedback">
                                        {{ $message }}
                                   </div>
                              @enderror
                         </div>
                         <button class="btn btn-primary" type="submit">Update</button>
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
