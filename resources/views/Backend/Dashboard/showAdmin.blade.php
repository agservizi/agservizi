@extends('Backend._layout._main')

@section('toolbar')
@endsection
@section('content')
    <div class="row g-6 g-xl-9 mb-3">
        @if(false)
        <div class="col-md-6 col-xl-4">
            <div  class="card mb-5">
                <div class="card-body p-9">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="fs-3 fw-bolder text-dark">{{ucfirst(\App\Models\Contratto::NOME_PLURALE)}}</div>
                        <div class="fs-2 fw-bolder" data-kt-countup="true" data-kt-countup-value="{{\App\Models\Contratto::count()}}" data-kt-countup-prefix="">0</div>
                    </div>
                    <a href="{{action([\App\Http\Controllers\Backend\ContrattoController::class,'create'])}}" class="btn btn-primary er w-100 fs-6 px-8 py-4" data-target="kt_modal" data-toggle="modal-ajax">Nuovo {{ucfirst(\App\Models\Contratto::NOME_SINGOLARE)}}</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div  class="card mb-5">
                <div class="card-body p-9">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="fs-3 fw-bolder text-dark">{{ucfirst(\App\Models\User::NOME_PLURALE)}}</div>
                        <div class="fs-2 fw-bolder" data-kt-countup="true" data-kt-countup-value="{{\App\Models\User::count()}}" data-kt-countup-prefix="">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div  class="card mb-5">
                <div class="card-body p-9">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="fs-3 fw-bolder text-dark">{{ucfirst(\App\Models\Documento::NOME_PLURALE)}}</div>
                        <div class="fs-2 fw-bolder" data-kt-countup="true" data-kt-countup-value="{{\App\Models\Documento::count()}}" data-kt-countup-prefix="">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div  class="card mb-5">
                <div class="card-body p-9">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="fs-3 fw-bolder text-dark">Documenti acquistati</div>
                        <div class="fs-2 fw-bolder" data-kt-countup="true" data-kt-countup-value="{{\App\Models\DocumentoAcquistato::count()}}" data-kt-countup-prefix="">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div  class="card mb-5">
                <div class="card-body p-9">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="fs-3 fw-bolder text-dark">Guadagno</div>
                        <div class="fs-2 fw-bolder" data-kt-countup="true" data-kt-countup-value="{{\App\Models\Pagamento::sum('importo')}}" data-kt-countup-prefix="€">0</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

@endsection
@push('customScript')
    <script>
        $(function () {
        });
    </script>
@endpush
