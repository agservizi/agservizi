<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
            <!--begin: Pic-->
            <!--end::Pic-->
            <!--begin::Info-->
            <div class="flex-grow-1">
                <!--begin::Title-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::User-->
                    <div class="d-flex flex-column">
                        <!--begin::Name-->
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-gray-900 fs-2 fw-bold me-1">{{$record->nominativo()}}</span>
                        </div>
                        <!--end::Name-->
                        <!--begin::Info-->
                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                            @if($record->email)
                                <span class="d-flex align-items-center text-gray-400  me-5 mb-2">
                                    <i class="las la-map-marker fs-4"></i>
                                    {{$record->email}}
                                </span>
                            @endif
                            @if($record->telefono)
                                <span class="d-flex align-items-center text-gray-400 me-5 mb-2 ">
                                    <i class="las la-map-marked fs-4"></i>{{$record->telefono}}
                                </span>
                            @endif
                        </div>
                        <!--end::Info-->
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Title-->
                <!--begin::Stats-->
                <div class="d-flex flex-wrap flex-stack">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <!--begin::Stats-->
                        <div class="d-flex flex-wrap">
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-400">Spedizioni:</div>
                                <!--end::Label-->
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <div class="fs-2 fw-bold">
                                        {{\App\Models\Spedizione::where('cliente_id',$record->id)->count()}}
                                    </div>
                                </div>
                                <!--end::Number-->
                            </div>
                            <!--end::Stat-->
                        </div>
                        <!--end::Stats-->
                    </div>
                    <!--end::Wrapper-->
                    <!--end::Progress-->
                </div>
                <!--end::Stats-->
            </div>
            <!--end::Info-->
        </div>
        <!--end::Details-->
    </div>
</div>

