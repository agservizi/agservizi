@extends('auth._main')
@section('content')
    <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif
        <form class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework" method="POST" action="{{ \Illuminate\Support\Facades\Route::has('login')?route('login'):'' }}">
        @csrf
        <!--begin::Heading-->
            <div class="text-center mb-10">
                <!--begin::Title-->
                <h1 class="text-dark mb-3">Accedi</h1>
                <!--end::Title-->
                @if($registrati && \Illuminate\Support\Facades\Route::has('register'))
                    <div class="text-gray-400 fw-bold fs-4">Sei nuovo?
                        <a href="{{ route('register') }}" class="link-primary fw-bolder">Crea un account</a>
                    </div>
                @endif
            </div>

            <!--begin::Heading-->
            <!--begin::Input group-->
            <div class="fv-row mb-10 fv-plugins-icon-container">
                <!--begin::Label-->
                <label class="form-label fs-6 fw-bolder text-dark required" for="email">Email</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input class="form-control form-control-lg form-control-solid @error('email') is-invalid @enderror" type="email" id="email" name="email" autocomplete="email"
                       value="{{ old('email') }}" required>
                <!--end::Input-->
                @error('email')
                <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="fv-row mb-10 fv-plugins-icon-container">
                <div class="d-flex flex-stack mb-2">
                    <label class="form-label fw-bolder text-dark fs-6 mb-0 required">Password</label>
                </div>
                <input class="form-control form-control-lg form-control-solid @error('password') is-invalid @enderror"
                       type="password" id="password" name="password"
                       autocomplete="current-password">
                @error('password')
                <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="w-100 text-end">
                    <a href="{{\Illuminate\Support\Facades\Route::has('password.request')? route('password.request') :''}}"
                       class="link-primary fs-6 fw-bolder">Hai dimenticato la
                        password ?</a>
                </div>
            </div>
            <div class="mb-10">
                <div class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ricordami</label>
                </div>
            </div>
            <!--end::Input group-->
            <!--begin::Actions-->
            <div class="text-center py-8">
                <!--begin::Submit button-->
                <button type="submit" id="kt_sign_in_submit" class="btn btn-lg btn-primary w-100 mb-5">
                    <span class="indicator-label">Accedi</span>
                </button>
            </div>
            <!--end::Actions-->
            <div></div>
            @if((env('APP_ENV')=='local' || config('configurazione.mostra_accessi_test')) && config('configurazione.accessi_test') )
                <h5>Dati accesso per test</h5>
                <table class="table table-row-dashed table-row-gray-300">
                    <thead>
                    <tr class="fw-bolder fs-6 text-gray-800">
                        <th>utente</th>
                        <th>email</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach(config('configurazione.accessi_test') as $accesso)
                        <tr class="fs-7">
                            <td>{{$accesso['descrizione']}}</td>
                            <td>{{$accesso['email']}}</td>
                            <td>
                                <button type="button" onClick="impostaLogin('{{$accesso['email']}}','{{$accesso['password']}}');">Usa</button>
                            </td>
                        </tr>
                    @endforeach
                    @foreach([]??\App\Models\User::where('id','>',2)->limit(2)->get() as $accesso)
                        <tr class="fs-7">
                            <td>Utente</td>
                            <td>{{$accesso->email}}</td>
                            <td>
                                <button type="button" onClick="impostaLogin('{{$accesso->email}}','password');">Usa</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

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
