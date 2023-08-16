@extends('auth._main')
@section('content')
    <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">

        @if (session('status'))

            @switch(session('status'))
                @case('verification-link-sent')
                <div class="alert alert-success" role="alert">
                    Email per la verifica del tuo indirizzo email <span class="fw-bold">{{\Illuminate\Support\Facades\Auth::user()->email}}</span> inviata
                </div>

                @break
                @default
                <div class="alert alert-danger" role="alert">
                    {{ session('status') }}
                </div>

            @endswitch
        @endif
        <form class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework" method="POST" action="{{ route('verification.send')}}">
        @csrf
        <!--begin::Heading-->
            <div class="text-center mb-10">
                <!--begin::Title-->
                <h1 class="text-dark mb-3">Verifica indirizzo email</h1>
                <!--end::Title-->
            </div>

            <!--begin::Heading-->
            <!--begin::Input group-->

            <div class="row">
                <div class="col-md-12 d-flex flex-column">
                    <div class="card flex-grow-1 mb-md-0 text-center">
                        <div class="card-body">
                            @if (session('resent'))
                                <div class="alert alert-success" role="alert">
                                    Una email di verifica è stata inviata al tuo indirizzo email {{Auth::user()->email}}
                                </div>
                            @endif
                            <p class="title-desc">Per accedere alla tua area riservata <strong>devi convalidare il tuo indirizzo email</strong> cliccando sul link presente nella
                                mail che ti
                                abbiamo
                                inviato</p>
                            <p class="title-desc">Se non hai ricevuto la mail ricontrolla tra qualche minuto, verifica nel filtro spam oppure
                                <button type="submit" class="btn btn-primary btn-sm">richiedi una nuova email di verifica</button>
                            </p>
                            link per test: <a href="{{session()->get('verificationUrl')}}">{{session()->get('verificationUrl')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('customScript')
    <script>
        function impostaLogin(email, password) {
            $('#email').val(email);
            $('#password').val(password);
        }
    </script>
@endpush
