<div class="card mb-5 mb-xl-8 ">
    <div class="card-body pt-15">
        <div class="d-flex flex-center flex-column mb-5">
            <div class="symbol symbol-100px symbol-circle mb-7">
                <div class="symbol symbol-50px symbol-circle">
                    <span class="symbol-label bg-success text-inverse-success fw-bolder fs-2hx">{{$record->iniziali()}}</span>
                </div>
            </div>

            <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bolder mb-1">{{$record->nominativo()}}</a>
            <div class="fs-5 fw-bold text-muted mb-6">{!! $record->userLevel(false,$record) !!}</div>
        </div>
        <div class="separator separator-dashed my-3"></div>
        <div id="kt_customer_view_details" class="collapse show">
            <div class="fs-6">
                <div class="fw-bolder mt-5">#</div>
                <div class="text-gray-600">{{$record->id}}</div>
                <div class="fw-bolder mt-5">Email</div>
                <div class="text-gray-600">
                    <a href="mailto:{{$record->email}}" class="text-gray-600 text-hover-primary">{{$record->email}}</a>
                </div>
                <div class="fw-bolder mt-5">Telefono</div>
                <div class="text-gray-600">
                    <a href="tel:{{$record->telefono}}" class="text-gray-600 text-hover-primary">{!! $record->telefonoWhatsapp() !!}</a>
                </div>
                <div class="fw-bolder mt-5">Codice fiscale</div>
                <div class="text-gray-600">{{$record->codice_fiscale}}</div>
                <div class="fw-bolder mt-5">Ultimo accesso</div>
                <div class="text-gray-600">{{$record->ultimo_accesso?$record->ultimo_accesso->format('d/m/Y H:i:s'):'Mai acceduto'}}</div>
                @if($ultimoAccesso)

                    @if($ultimoAccesso->user_agent)
                        @php($result=Browser::parse($ultimoAccesso->user_agent))
                        <div class="fw-bolder mt-5">User agent</div>
                        <div class="text-gray-600">{{$result->platformName()}}, {{$result->browserName()}}, {{$result->deviceModel()}}</div>
                    @endif
                @endif
                <div class="fw-bolder mt-5">Data creazione</div>
                <div class="text-gray-600">{{$record->created_at->format('d/m/Y')}}</div>
            </div>
        </div>
    </div>
</div>
