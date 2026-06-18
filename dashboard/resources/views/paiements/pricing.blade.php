@extends('layout.master')

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Paiement</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pricing</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center mb-3 mt-4">Choisis un mode de paiement</h2>
            <p class="text-muted text-center mb-4 pb-2">Choose the features and functionality your team need today.
                Easily upgrade as your company grows.</p>
            <div class="container">
                <div class="row">
                    <div class="col-md-4 stretch-card grid-margin grid-margin-md-0">
                        <div class="card">
                            <div class="card-body">
                                {{--                                <h4 class="text-center mt-3 mb-4">Orange Monney</h4>--}}
                                {{--                                <i data-feather="award" class="text-primary icon-xxl d-block mx-auto my-3"></i>--}}
                                <img src="{{asset("assets/images/others/Logo_Orange_Money.svg.png")}}"
                                     class="icon-xxl d-block mx-auto my-3" width="115px"
                                     height="51px" alt="Orange Monney">
                                <div class="d-flex justify-content-center align-items-baseline">
                                    <span class="fs-3 fw-bold">
                                        {{ number_format($reservation['total_price'], 0, ',', ' ') }}
                                    </span>
                                    <span class="ms-2 text-muted small">FCFA</span>
                                </div>
                                <p class="text-muted text-center mb-4 fw-light">{{$reservation["annonce"]['origin']}}
                                    - {{$reservation["annonce"]['destination']}}</p>
                                <h5 class="text-primary text-center mb-4">Up to 25 units</h5>
                                <table class="mx-auto">
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Accounting dashboard</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Invoicing</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Online payments</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="x" class="icon-md text-danger me-2"></i></td>
                                        <td><p class="text-muted">Branded website</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="x" class="icon-md text-danger me-2"></i></td>
                                        <td><p class="text-muted">Dedicated account manager</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="x" class="icon-md text-danger me-2"></i></td>
                                        <td><p class="text-muted">Premium apps</p></td>
                                    </tr>
                                </table>
                                <div class="d-grid">
                                    <button class="btn btn-primary mt-4">Start free trial</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 stretch-card grid-margin grid-margin-md-0">
                        <div class="card">
                            <div class="card-body">
                                {{--<h4 class="text-center mt-3 mb-4">Wave</h4>
                                <i data-feather="gift" class="text-success icon-xxl d-block mx-auto my-3"></i>--}}
                                <img src="{{asset("assets/images/others/wave-logo.png")}}"
                                     class="icon-xxl d-block mx-auto my-3" width="115px"
                                     height="51px" alt="Wave">
                                <div class="d-flex justify-content-center align-items-baseline">
                                    <span class="fs-3 fw-bold">
                                        {{ number_format($reservation['total_price'], 0, ',', ' ') }}
                                    </span>
                                    <span class="ms-2 text-muted small">FCFA</span>
                                </div>
                                <p class="text-muted text-center mb-4 fw-light">{{$reservation["annonce"]['origin']}}
                                    - {{$reservation["annonce"]['destination']}}</p>
                                <h5 class="text-success text-center mb-4">Up to 75 units</h5>
                                <table class="mx-auto">
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Accounting dashboard</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Invoicing</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Online payments</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Branded website</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Dedicated account manager</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="x" class="icon-md text-danger me-2"></i></td>
                                        <td><p class="text-muted">Premium apps</p></td>
                                    </tr>
                                </table>
                                <div class="d-grid">
                                    <button class="btn btn-success mt-4">Start free trial</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="25" fill="#031323"
                                     class=" icon-xxl d-block mx-auto my-3" viewBox="0 0 60 25"
                                     aria-label="Logo Stripe">
                                    <path fill="var(--hds-color-text-solid)" fill-rule="evenodd"
                                          d="M59.6444 14.2813h-8.062c.1843 1.9296 1.5983 2.5476 3.2032 2.5476 1.6352 0 2.9534-.3656 4.0453-.9506v3.3179c-1.1186.7115-2.5964 1.1068-4.5645 1.1068-4.011 0-6.8218-2.5122-6.8218-7.4783 0-4.19441 2.3837-7.52509 6.3017-7.52509 3.912 0 5.9537 3.28038 5.9537 7.49819 0 .3982-.0372 1.261-.0556 1.4835Zm-5.9241-5.62407c-1.0294 0-2.1739.72812-2.1739 2.58387h4.2573c0-1.85362-1.0721-2.58387-2.0834-2.58387ZM40.9547 20.303c-1.4411 0-2.322-.6087-2.9133-1.0417l-.0088 4.6271-4.1181.8755-.0014-19.19053h3.7543l.0864 1.01784c.6035-.52914 1.6114-1.29157 3.2256-1.29162 2.8925 0 5.6162 2.6052 5.6162 7.39971 0 5.2327-2.6948 7.6037-5.6409 7.6037Zm-.959-11.35573c-.9453 0-1.5376.34559-1.9669.81586l.0245 6.11967c.3997.433.9763.7813 1.9424.7813 1.5231 0 2.5437-1.6575 2.5437-3.8745 0-2.1544-1.037-3.84233-2.5437-3.84233Zm-11.7602-3.3739h4.1341V20.0088h-4.1341V5.57337Zm0-4.694699L32.3696 0v3.35821l-4.1341.87868V.878671ZM23.9198 10.2223v9.7861h-4.1156V5.57296h3.6867l.1317 1.21751c1.0035-1.7722 3.0722-1.41321 3.6209-1.21594v3.78524c-.5242-.16908-2.2894-.42779-3.3237.86253Zm-8.5525 4.7221c0 2.4275 2.5988 1.6719 3.1263 1.4609v3.3522c-.5492.3013-1.5437.5458-2.8901.5458-2.4441 0-4.2773-1.7999-4.2773-4.2379l.0173-13.17658 4.0206-.85464.0032 3.5395h3.1278V9.0857h-3.1278v5.8588-.0001Zm-4.9069.7026c0 2.9645-2.31051 4.6562-5.73464 4.6562-1.41958 0-2.92289-.2761-4.453935-.9347v-3.9319c1.382085.7516 3.093705 1.315 4.457755 1.315.91864 0 1.53106-.2459 1.53106-1.0069C6.26064 13.7786 0 14.5192 0 9.95995 0 7.04457 2.27622 5.2998 5.61655 5.2998c1.36404 0 2.72806.20934 4.09208.75351V9.9317c-1.25265-.67618-2.84332-1.05979-4.09588-1.05979-.86296 0-1.44753.24965-1.44753.8924.0001 1.85329 6.29518.97249 6.29518 5.88279v-.0001Z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                <div class="d-flex justify-content-center align-items-baseline">
                                    <span class="fs-3 fw-bold">
                                        {{ number_format($reservation['total_price'], 0, ',', ' ') }}
                                    </span>
                                    <span class="ms-2 text-muted small">FCFA</span>
                                </div>
                                <p class="text-muted text-center mb-4 fw-light">{{$reservation["annonce"]['origin']}}
                                    - {{$reservation["annonce"]['destination']}}</p>
                                <h5 class="text-primary text-center mb-4">Up to 300 units</h5>
                                <table class="mx-auto">
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Accounting dashboard</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Invoicing</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Online payments</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Branded website</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Dedicated account manager</p></td>
                                    </tr>
                                    <tr>
                                        <td><i data-feather="check" class="icon-md text-primary me-2"></i></td>
                                        <td><p>Premium apps</p></td>
                                    </tr>
                                </table>
                                <form action="{{route("paiements.stripe")}}">
                                    @csrf
                                    @method('POST')
                                <input hidden="hidden" name="id" value="{{$reservation['id']}}">
                                <div class="d-grid">
                                    <button class="btn btn-primary mt-4">Valider</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
