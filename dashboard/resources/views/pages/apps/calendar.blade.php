@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/fullcalendar/main.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-3 d-none d-md-block">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title mb-4">Full calendar</h6>
            <div id='external-events' class='external-events'>
              <h6 class="mb-2 text-muted">Draggable Events</h6>
              <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
                <div class='fc-event-main'>Birth Day</div>
              </div>
              <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
                <div class='fc-event-main'>New Project</div>
              </div>
              <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
                <div class='fc-event-main'>Anniversary</div>
              </div>
              <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
                <div class='fc-event-main'>Clent Meeting</div>
              </div>
              <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>
                <div class='fc-event-main'>Office Trip</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-9">
        <div class="card">
          <div class="card-body">
            <div id='fullcalendar'>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="fullCalModal" class="modal fade">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="modalTitle1" class="modal-title"></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><span class="visually-hidden">close</span></button>
      </div>
      <div id="modalBody1" class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary">Event Page</button>
      </div>
    </div>
  </div>
</div>

<div id="createEventModal" class="modal fade">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="modalTitle2" class="modal-title">Add event</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><span class="visually-hidden">close</span></button>
      </div>
      <div id="modalBody2" class="modal-body">
          <form action="{{ route('meals.store') }}" method="POST">
              @csrf
              {{--<div class="mb-3">
                  <label class="form-label" for="user">User</label>
                  <select name="user" id="user" class="form-control @error("user") is-invalid @enderror" required>
                      <option value="">Choose user</option>
                       @foreach($users as $user)
                          <option value="{{ $user->id }}">{{ $user->name }}</option>
                       @endforeach
                  </select>
                  @error("user")
                  <div class="invalid-feedback">
                      {{ $message }}
                  </div>
                  @enderror
              </div>
              <div class="mb-3">
                  <label class="form-label">Recipe</label>
                  <select name="recipe" id="recipe" class="form-control @error("recipe") is-invalid @enderror" required>
                      <option value="">Choose recipe</option>
                        @foreach($annonces as $recipe)
                          <option value="{{ $recipe->id }}">{{ $recipe->title }}</option>
                        @endforeach
                  </select>
                  @error("recipe")
                  <div class="invalid-feedback">
                      {{ $message }}
                  </div>
                  @enderror
              </div>--}}
              <div class="mb-3">
                  <label class="form-label" for="type">Type Meal</label>
                  <select name="type" id="type" class="form-control @error("type") is-invalid @enderror" required>
                      <option value="">Choose type</option>
                      <option value="breakfast">Breakfast</option>
                      <option value="lunch">Lunch</option>
                      <option value="dinner">Dinner</option>
                      <option value="snack">Snack</option>
                  </select>
                  @error("type")
                  <div class="invalid-feedback">
                      {{ $message }}
                  </div>
                  @enderror
              </div>
              <div class="mb-3">
                  <label for="date" class="form-label">Date</label>
                  <input type="date" name="date" id="date" class="form-control @error("date") is-invalid @enderror" required />
                  @error("date")
                  <div class="invalid-feedback">
                      {{ $message }}
                  </div>
                  @enderror
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="submit">Add</button>
              </div>
          </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/fullcalendar/index.global.min.js') }}"></script>
@endpush

@push('custom-scripts')
  <script src="{{ asset('assets/js/fullcalendar.js') }}"></script>
@endpush
