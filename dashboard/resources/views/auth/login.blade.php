@extends('layout.master2')

@section('content')
<div class="page-content d-flex align-items-center justify-content-center">

  <div class="row w-100 mx-0 auth-page">
    <div class="col-md-6 col-xl-4 mx-auto">
      <div class="card">
        <div class="row">
          <div class="col-md-4 pe-md-0">

          </div>
        </div>
          <div class="col-md-12 ps-md-0">
            <div class="auth-form-wrapper px-4 py-5">
              <a href="#" class="noble-ui-logo d-block text-center mb-2">Sama<span>GP</span></a>
              <h5 class="text-muted fw-normal text-center mb-4">Welcome back! Log in to your account.</h5>
                @if(session('error'))
                    <div class="alert alert-danger">
                        <p class="text-center">{{ session('error') }}</p>
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">
                        <p class="text-center">{{ session('success') }}</p>
                    </div>
                @endif

              <form class="forms-sample" action="{{route('login')}}" method="POST">
                  @csrf
                  <div class="mb-3">
                      <label for="email" class="form-label">Email address</label>
                      <input type="email"
                             class="form-control @if($errors->any()) is-invalid @endif"
                             name="email"
                             id="email"
                             placeholder="Email">
                      @if($errors->any())
                      <div class="invalid-feedback">
                          Identifiants Incorrects
                      </div>
                      @endif
                  </div>
                <div class="mb-3">
                  <label for="userPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" name="password" id="userPassword" autocomplete="current-password" placeholder="Password">
                </div>
                {{--<div class="form-check mb-3">
                  <input type="checkbox" class="form-check-input" id="authCheck">
                  <label class="form-check-label" for="authCheck">
                    Remember me
                  </label>
                </div>--}}

                  <div>
                      <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0">Login</button>
                      <button type="button" class="btn btn-outline-primary btn-icon-text mb-2 mb-md-0">
                          <a href="{{route('google')}}"  class=" mb-2 mb-md-0">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google" viewBox="0 0 16 16">
                                  <path d="M15.545 6.558a9.4 9.4 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.7 7.7 0 0 1 5.352 2.082l-2.284 2.284A4.35 4.35 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.8 4.8 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.7 3.7 0 0 0 1.599-2.431H8v-3.08z"/>
                              </svg>
                              Login with google
                          </a>

                      </button>
                  </div>

                <a href="{{ url('/register') }}" class="d-block mt-3 text-muted">Not a user? Sign up</a>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
