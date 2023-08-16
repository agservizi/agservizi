@extends('auth._main')
@section('content')
    <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">

        <form class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <div class="text-center mb-10">
                <h1 class="text-dark mb-3">Imposta la password</h1>
            </div>
            <div class="fv-row mb-10 fv-plugins-icon-container">
                <label class="form-label fs-6 fw-bolder text-dark required" for="email">Email</label>
                <input class="form-control form-control-lg form-control-solid @error('email') is-invalid @enderror" type="email" id="email" name="email" autocomplete="email"
                       value="{{ old('email',request()->input('email')) }}" required>
                @error('email')
                <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="fv-row mb-10 fv-plugins-icon-container">
                <div class="d-flex flex-stack mb-2">
                    <label class="form-label fw-bolder text-dark fs-6 mb-0 required" for="password">Password</label>
                </div>
                <input class="form-control form-control-lg form-control-solid @error('password') is-invalid @enderror" type="password" id="password" name="password"
                       autocomplete="new-password">
                @error('password')
                <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="fv-row mb-10 fv-plugins-icon-container">
                <div class="d-flex flex-stack mb-2">
                    <label class="form-label fw-bolder text-dark fs-6 mb-0 required" for="password_confirmation">Conferma password</label>
                </div>
                <input class="form-control form-control-lg form-control-solid " type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
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
