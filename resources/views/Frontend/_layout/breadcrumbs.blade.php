<ul class="breadcrumb breadcrumb-separatorless fw-semibold mb-3">

    <!--begin::Item-->
    <li class="breadcrumb-item text-gray-600 fw-bold lh-1">
        <a href="/"
           class="text-white text-hover-primary">
            <i class="ki-duotone ki-home text-gray-500 fs-2"></i> </a>
    </li>
    @foreach($breadcrumbs as $url=>$testo)
        <li class="breadcrumb-item text-muted">
            <a href="{{$url}}" class="text-muted text-hover-primary">{{$testo}}</a>
        </li>
        <li class="breadcrumb-item text-gray-600 fw-bold lh-1">
            <a href="{{$url}}" class="text-muted text-hover-primary">{{$testo}}</a>
        </li>
        @if($loop->remaining>1)
            <li class="breadcrumb-item">
                <i class="ki-duotone ki-right fs-3 text-gray-500 mx-n1"></i>
            </li>
        @endif
    @endforeach
</ul>

