@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Roles et permessions</li>
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
          <div class="d-flex justify-content-between align-items my-3">
              <h6 class="card-title">Roles et permessions</h6>
              <a href="{{route('roles.create')}}" class="btn btn-primary">Ajouter un nouveau role</a>
          </div>

          <div class="table-responsive">
            <table id="dataTableExample" class="table table-hover">
              <thead>
                <tr>
                    <th>Role</th>
                    <th>Autorisations</th>
                    <th class="no-sort">Actions</th>
                </tr>
              </thead>
              <tbody>
                @if($roles)
                  @foreach($roles as $role)
                    <tr>
                        <td>{{$role->name}}</td>
                        <td>
                          @foreach ($role->permissions as $permession)
                              <div class="badge rounded-pill bg-primary text-capitalize">
                                  {{ str_replace('_', ' ', $permession->name) }}
                              </div>
                          @endforeach
                        </td>
                        <td class="actions">
                          <form action="{{ route('roles.destroy',$role->id) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <button class="btn btn-sm btn-danger circle">
                                    <i data-feather="trash"></i>
                              </button>
                          </form>
                          <a class="btn btn-sm btn-success circle" href="{{route('roles.edit',$role->id)}}">
                              <i data-feather="edit"></i>
                          </a>
                        </td>
                    </tr>
                  @endforeach
                @endif
              </tbody>  
            </table>
            {{-- {{$roles->links()}}--}}
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
@endpush