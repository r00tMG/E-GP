@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
@endpush

@section('content')
   {{-- @dump($users)--}}

   <nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Les utilisateurs</li>
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
            <h6 class="card-title">Les utilisateurs</h6>
            <a href="{{route('users.create')}}" class="btn btn-primary">Create</a>
        </div>
        <div class="table-responsive">
            @if(session('success'))
                <div class="w-50 m-auto alert bg-alert-success">
                    <p class="text-center">{{ session('success') }}</p>
                </div>
            @endif
          <table id="dataTableExample" class="table table-bordered">
               <thead>
                    <tr>
                         <th>Name</th>
                         <th>Email</th>
                         <th>Role</th>
                         <th class="no-sort" width="10%">Actions</th>
                    </tr>
               </thead>
               <tbody>
               @if($users)
                    @foreach($users as $user)
                    <tr>
                      <td>{{$user->name}}</td>
                      <td>{{$user->email}}</td>
                      <td>
                      @foreach ($user->roles as $role)
                        <div class="badge bg-primary">{{ $role->name }}</div>
                      @endforeach
                      </td>
                      <td class="actions">
                        <form action="{{ route('users.destroy',$user->id) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-sm btn-danger circle">
                              <i data-feather="trash"></i>
                          </button>
                        </form>
                        <a class="btn btn-sm btn-success circle" href="{{route('users.edit',$user->id)}}">
                          <i data-feather="edit"></i>
                        </a>
                      </td>
                    </tr>
                    @endforeach
               @endif
               </tbody>  
          </table>
          {{-- {{$users->links()}}--}}
        </div>
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