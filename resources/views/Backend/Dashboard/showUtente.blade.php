@extends('Backend._layout._main')

@section('toolbar')
@endsection
@section('content')




@endsection
@push('customCss')
    @livewireStyles
@endpush
@push('customScript')
    @livewireScripts
    <script type="text/javascript" src="/assets_backend/js-miei/moment_it.js"></script>
    <script src="/assets_backend/js-miei/flatPicker_it.js"></script>

    <script>
        $(function () {

        });
    </script>
@endpush
