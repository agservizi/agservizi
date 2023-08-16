@extends('errors.minimal')
@section('toolbar')
@endsection
@section('content')
    <div class="d-flex flex-column flex-center flex-column-fluid">
        <!--begin::Content-->
        <div class="d-flex flex-column flex-center text-center p-10">
            <!--begin::Wrapper-->
            <div class="card card-flush w-lg-650px py-5">
                <div class="card-body py-15 py-lg-20">
                    <!--begin::Title-->
                    <h1 class="fw-bolder fs-2hx text-gray-900 mb-4">Oops! Pagina non trovata</h1>
                    <!--end::Title-->
                    <!--begin::Text-->
                    <div class="fw-semibold fs-6 text-gray-500 mb-7">{{$exception->getMessage()}}</div>
                    <!--end::Text-->
                    <!--begin::Illustration-->
                    <div class="mb-3">
                        <img src="/assets_backend/media/auth/404-error.png" class="mw-100 mh-300px theme-light-show" alt="" />
                        <img src="/assets_backend/media/auth/404-error-dark.png" class="mw-100 mh-300px theme-dark-show" alt="" />
                    </div>
                    <!--end::Illustration-->
                    <!--begin::Link-->
                    <div class="mb-0">
                        <a href="{{url()->previous()}}" class="btn btn-sm btn-primary">Torna indietro</a>
                    </div>
                    <!--end::Link-->
                </div>
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Content-->
    </div>

@endsection
@section('customScript')
@endsection
