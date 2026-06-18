@extends('layout.master2')

@section('content')
<div class="page-content d-flex align-items-center justify-content-center">

  <div class="row w-100 mx-0 auth-page">
    <div class="col-md-8 col-xl-6 mx-auto">
      <div class="card">
        <div class="row">
          <div class="col-md-4 pe-md-0">
            <div class="auth-side-wrapper" style="background-image: url({{ url('https://via.placeholder.com/219x452') }})">

            </div>
          </div>
          <div class="col-md-12 ps-md-0">
            <div class="auth-form-wrapper px-4 py-5">
                <a href="#" class="noble-ui-logo d-block text-center mb-2">Sama<span>GP</span></a>
                @if(session('success'))
                    <div class="alert alert-success">
                        <p class="text-center">{{ session('success') }}</p>
                    </div>
                @else
              <h5 class="text-muted fw-normal text-center mb-4">Create a free account.</h5>
                @endif


              <form action="{{route('register')}}" method="POST" class="forms-sample">
                  @csrf
                  {{--email
                    phone
                    password
                    confirm_password
                    role--}}
                <div class="mb-3">
                  <label for="exampleInputUsername1" class="form-label">Email</label>
                  <input type="email" class="form-control"  id="exampleInputUsername1" name="email" autocomplete="Username" placeholder="Email">
                    @if(session('error'))
                            <p class="alert alert-danger text-center">{{ session('error')[0]['msg'] }}</p>
                    @endif
                </div>
                <div class="mb-3">
                  <label for="phone" class="form-label">Phone</label>
                  <input type="text" class="form-control" id="phone"  name="phone" placeholder="Phone">
                    @if(session('error'))
                        <p class="alert alert-danger text-center">{{ session('error')[0]['msg'] }}</p>
                    @endif
                </div>
                <div class="mb-3">
                  <label for="userPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" id="userPassword"  name="password" autocomplete="current-password" placeholder="Password">
                    @if(session('error'))
                        <p class="alert alert-danger text-center">{{ session('error')[0]['msg'] }}</p>
                    @endif
                </div>
                  <div class="mb-3">
                      <label for="userPassword" class="form-label">Confirmation Password</label>
                      <input type="password" class="form-control" id="userPassword"  name="confirm_password" autocomplete="current-password" placeholder="Password">
                      @if(session('error'))
                          <p class="alert alert-danger text-center">{{ session('error')[0]['msg'] }}</p>
                      @endif
                  </div>
                  <div class="mb-3">
                      <label for="userPassword" class="form-label">Role</label>
                      <select class="form-select" name="role"  id="ageSelect">
                          <option selected disabled>Select your role</option>
                          <option value="client">CLIENT</option>
                          <option value="gp">GP</option>
                      </select>
                      @if(session('error'))
                          <p class="alert alert-danger text-center">{{ session('error')[0]['msg'] }}</p>
                      @endif
                  </div>
                <div class="form-check mb-3">
                  <input type="checkbox" class="form-check-input" id="authCheck">
                  <label class="form-check-label" for="authCheck">
                    Remember me
                  </label>
                </div>
                <div>
                  <button type="submit"  class="btn btn-primary me-2 mb-2 mb-md-0">Sign up</button>
                  <button type="button" class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                    <i class="btn-icon-prepend" data-feather="twitter"></i>
                    Sign up with twitter
                  </button>
                </div>
                <a href="{{ url('/login') }}" class="d-block mt-3 text-muted">Already a user? Sign in</a>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
