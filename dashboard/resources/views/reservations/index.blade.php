@extends('layout.master')

@push('plugin-styles')
    <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet"/>
@endpush

@section('content')
    {{-- @dump($annonces)--}}
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Reservations</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            @if(Session::has("success"))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong class="text-center">{{ Session::get("success") }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                </div>
            @endif

            @if(Session::has("error"))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong class="text-center">{{ Session::get("error") }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                </div>
            @endif
        </div>

        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items my-3">
                        <h6 class="card-title">Reservations</h6>
{{--                        <a href="{{route('annonces.create')}}" class="btn btn-primary">New Reservations</a>--}}
                    </div>
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="no-sort">GP</th>
                                <th class="no-sort">Client</th>
                                <th>Départ</th>
                                <th>Arrivée</th>
                                <th>Date Départ</th>
                                <th>Date Arrivée</th>
                                <th>Status</th>
                                <th class="no-sort">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($reservations)
                                @foreach($reservations as $reservation)
                                    <tr>
                                        <td>
                                            <a class="btn btn-sm btn-transparent border border-primary circle" href="#">
                                                <img src="#" alt="Profile" >{{$reservation['annonce']['gp']['email']}}
                                            </a>
                                        <td>
                                            <a class="btn btn-sm btn-transparent border border-primary circle" href="#">
                                                <img src="#" alt="Profile" >{{$reservation['user']['email']}}
                                            </a>
                                        </td>
                                        <td>{{$reservation['annonce']['origin']}}</td>
                                        <td>{{$reservation['annonce']['destination']}}</td>
                                        <td>
                                            <div
                                                class="badge bg-primary">{{$reservation['annonce']['date_depart']}}</div>
                                        </td>
                                        <td>
                                            <div
                                                class="badge bg-primary">{{$reservation['annonce']['date_arrivee']}}</div>
                                        </td>
                                        <td>
                                            @if($reservation['status'] == "pending")
                                                <div class="badge bg-warning">{{$reservation['status']}}</div>
                                            @elseif($reservation['status'] == "confirmed")
                                                <div class="badge bg-success">{{$reservation['status']}}</div>
                                            @else
                                                <div class="badge bg-danger">{{$reservation['status']}}</div>
                                            @endif
                                        </td>
                                        {{--
                                            <i data-feather="eye" class="icon-sm me-2"></i>
                                            <i data-feather="edit-2" class="icon-sm me-2"></i>
                                            <i data-feather="trash" class="icon-sm me-2"></i>
                                            <i data-feather="printer" class="icon-sm me-2"></i>
                                            <i data-feather="download" class="icon-sm me-2"></i>
                                            --}}
                                        <td class="actions">
                                            <form action="{{route("reservations.destroy", $reservation['id'])}}"
                                                  method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger circle">
                                                    <i data-feather="trash" class="icon-sm "></i>
                                                </button>
                                            </form>
                                            <a class="btn btn-sm btn-success circle"
                                               href="{{route('reservations.edit', $reservation['id'])}}">
                                                <i data-feather="edit-2" class="icon-sm"></i>
                                            </a>
                                            <a class="btn btn-sm btn-warning circle"
                                               href="{{route('reservations.show', $reservation['id'])}}">
                                                <i data-feather="eye" class="text-white"></i>
                                            </a>

                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                            </tbody>
                        </table>
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
