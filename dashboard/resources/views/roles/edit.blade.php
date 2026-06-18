@extends('layout.master')

@push('plugin-styles')
<link href="{{ asset('assets/plugins/jquery-tags-input/jquery.tagsinput.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/plugins/pickr/themes/classic.min.css') }}" rel="stylesheet" />
@endpush

@section('content')

<nav class="page-breadcrumb">
     <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Roles et permessions</a></li>
          <li class="breadcrumb-item active" aria-current="page">Nouveau role</li>
     </ol>
</nav>

<div class="row">
     <div class="w-75 m-auto grid-margin stretch-card">
          <div class="card">
               <div class="card-header">
                    <h5 class="mb-0 h6">{{__('Ajouter un nouveau rôle')}}</h5>
               </div>
               <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                         <div class="form-group mb-3">
                              <label class="from-label" for="name">{{__('Nom du rôle')}}</label>
                              <input type="text" placeholder="{{__('Nom du rôle')}}" id="name" name="name" class="form-control" value="{{ $role->name }}" required>
                         </div>
                         <br>
                         <div class="mb-3">
                            <label for="permission" class="form-label"><strong>Permissions:</strong></label>
                            <div class="list-group">
                                @foreach ($permission as $value)
                                    <label class="list-group-item d-flex align-items-center">
                                        <input type="checkbox" name="permission[]" value="{{ $value->id }}"
                                               class="form-check-input me-3" id="permission{{ $value->id }}"
                                            {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>
                                        {{ $value->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                         <div class="form-group mb-3 mt-3 text-right">
                              <button type="submit" class="btn btn-primary">{{__('Update')}}</button>
                         </div>
                    </div>
               </form>
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

     <script>
          $(document).ready(function() {
              $('#selectAll').click(function() {
                  console.log(this.checked);
                  $('input:checkbox').not(this).attr('checked', true)
              });
          });
      </script>
@endpush
