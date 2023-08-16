@extends('auth._main')
@section('content')
    <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <form class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework" method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="text-center mb-10">
            <h1 class="text-dark mb-3">Hai dimenticato la password</h1>
            <p class="font-size-16 font-weight-medium text-start">Inserisci l'email del tuo account.<br>
                Riceverai una mail con un collegamento per reimpostare la password<br>
            </p>
        </div>
        <div class="fv-row mb-10 fv-plugins-icon-container">
            <label class="form-label fs-6 fw-bolder text-dark required">Email</label>
            <input class="form-control form-control-lg form-control-solid @error('email') is-invalid @enderror" type="email" name="email" autocomplete="email"
                   value="{{ old('email') }}" required>
            @error('email')
            <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="text-center">
            <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
                <span class="indicator-label">Invia</span>
            </button>
        </div>
        <div></div>
    </form>
    </div>
@endsection
