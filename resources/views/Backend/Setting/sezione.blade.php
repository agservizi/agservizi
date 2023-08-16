@php($breadcrumbs=[action([$controller,'index'])=>'Torna a indice impostazioni'])

@extends('Backend._layout._main')
@section('content')
    <div class="card">
        <div class="card-body">
            @include('Backend._components.alertErrori')
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <div class="row">
                <div class="col-md-10">
                    <form method="post" action="{{ route('settings.store',$sezione) }}" class="form-horizontal" role="form" id="form-sezione">
                        {!! csrf_field() !!}
                        @php($section=config('setting_fields.'.$sezione, []))
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <i class="{{ \Illuminate\Support\Arr::get($section, 'icon', 'glyphicon glyphicon-flash') }}"></i>
                                <h4> {{ $section['title'] }}</h4>
                            </div>
                            <p class="fw-bold">{{ $section['desc'] }}</p>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        @foreach($section['elements'] as $field)
                                            @includeIf('Backend.Setting.Fields.' . $field['type'] )
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end panel for {{ $section['title'] }} -->
                        <div class="w-100 text-center m-b-md">
                            <button class="btn-primary btn">
                                Salva impostazioni
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                </div>
            </div>
        </div>
    </div>
@endsection
@push('customScript')
    <script src="/assets_backend/js-progetto/ckeditor/ckeditor.js?v=1"></script>
    <script src="/assets_backend/js-miei/autoNumeric.js"></script>
    <script>
        $(function () {
            autonumericImporto('importo');
            $('.editor').each(function () {
                CKEDITOR.replace($(this).attr('id'),
                );
            });
        });
    </script>
@endpush


