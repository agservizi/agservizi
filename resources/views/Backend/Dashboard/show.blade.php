@extends('Backend._layout._main')
@section('content')
    <div class="row g-5">
        <div class="col-xl-12">
            <div class="card card-stretch">
                <div class="card-body py-10 ">
                    <div class="row">
                        @foreach(\App\Models\StatoSpedizione::withCount('spedizioni')->get() as $stato)

                            <!--begin::Col-->
                            <div class="col">
                                <div class="card card-dashed flex-center min-w-175px my-3 p-6" style="background-color: {{$stato->colore_hex}};">
                                    <span class="fs-4 fw-bold  pb-1 px-2">{!! $stato->badgeStato() !!}</span>
                                    <span class="fs-lg-2tx fw-bold d-flex justify-content-center">
														<span data-kt-countup="true"
                                                              data-kt-countup-value="{{$stato->spedizioni_count}}">0</span></span>
                                </div>
                            </div>
                            <!--end::Col-->
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <!--begin::Col-->
        <div class="col-xl-12">
            <!--begin::Chart Widget 1-->
            <div class="card card-flush h-lg-100">
                <!--begin::Header-->
                <div class="card-header pt-5">
                    <!--begin::Title-->
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Spedizioni {{$grafico['anno']}}</span>
                    </h3>
                    <!--end::Title-->
                    <!--begin::Toolbar-->
                    <div class="card-toolbar">


                    </div>
                    <!--end::Toolbar-->
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="card-body pt-0 px-0">
                    <!--begin::Chart-->
                    <div id="kt_charts_widget_1" class="min-h-auto ps-4 pe-6 mb-3" style="height: 350px"></div>
                    <!--end::Chart-->
                </div>
                <!--end::Body-->
            </div>
            <!--end::Chart Widget 1-->
        </div>
        <!--end::Col-->
    </div>
@endsection
@push('customCss')
@endpush
@push('customScript')
    <script>
        $(function () {
            var KTChartsWidget1 = function () {
                var e = {self: null, rendered: !1}, t = function () {
                    var t = document.getElementById("kt_charts_widget_1");
                    if (t) {
                        var a = t.hasAttribute("data-kt-negative-color") ? t.getAttribute("data-kt-negative-color") : KTUtil.getCssVariableValue("--kt-danger"),
                            l = parseInt(KTUtil.css(t, "height")), r = KTUtil.getCssVariableValue("--kt-gray-500"),
                            o = KTUtil.getCssVariableValue("--kt-border-dashed-color"),
                            i = {
                                series: [{
                                    name: "Spedizioni",
                                    data: @json($grafico['arrDati']['conteggio'])
                                }],
                                chart: {
                                    fontFamily: "inherit",
                                    type: "bar",
                                    stacked: !0,
                                    height: l,
                                    toolbar: {show: !1}
                                },
                                plotOptions: {bar: {columnWidth: "35%", barHeight: "70%", borderRadius: [6, 6]}},
                                legend: {show: !1},
                                dataLabels: {enabled: !1},
                                xaxis: {
                                    categories: @json($grafico['arrDati']['labels']),
                                    axisBorder: {show: !1},
                                    axisTicks: {show: !1},
                                    tickAmount: 10,
                                    labels: {style: {colors: [r], fontSize: "12px"}},
                                    crosshairs: {show: !1}
                                },
                                yaxis: {
                                    //min: -50,
                                    //max: 80,
                                    tickAmount: 6, labels: {
                                        style: {colors: [r], fontSize: "12px"}, formatter: function (e) {
                                            return parseInt(e) + ""
                                        }
                                    }
                                },
                                fill: {opacity: 1},
                                states: {
                                    normal: {filter: {type: "none", value: 0}},
                                    hover: {filter: {type: "none", value: 0}},
                                    active: {allowMultipleDataPointsSelection: !1, filter: {type: "none", value: 0}}
                                },
                                tooltip: {
                                    style: {fontSize: "12px", borderRadius: 4}, y: {
                                        formatter: function (e) {
                                            return e > 0 ? e + "" : Math.abs(e)
                                        }
                                    }
                                },
                                colors: [KTUtil.getCssVariableValue("--kt-success"), a],
                                grid: {borderColor: o, strokeDashArray: 4, yaxis: {lines: {show: !0}}}
                            };
                        e.self = new ApexCharts(t, i), setTimeout((function () {
                            e.self.render(), e.rendered = !0
                        }), 200)
                    }
                };
                return {
                    init: function () {
                        t();
                    }
                }
            }();
            KTChartsWidget1.init();
        });
    </script>
@endpush
