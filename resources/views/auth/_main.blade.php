<html lang="it">
<head>
    <meta charset="utf-8"/>
    <title>{{$tagTitle??config('configurazione.tagTitle')}}</title>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
    <link href="/assets_backend/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css">
    <link href="/assets_backend/css/style.bundle.css" rel="stylesheet" type="text/css">

    <!--begin::favicon-->
    <link rel="shortcut icon" href="/loghi/favicon.ico"/>
    <link rel="apple-touch-icon" sizes="180x180" href="/loghi/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/loghi/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/loghi/favicon-16x16.png">
    <link rel="manifest" href="/loghi/site.webmanifest">
    <link rel="mask-icon" href="/loghi/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <meta name="google-site-verification" content=""/>
</head>
<body id="kt_body" class="bg-body">
<div class="d-flex flex-column flex-root">
    <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed "
         style="background-image: url(/loghi/sfondo-login.png)">
        <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20 ">
            <a href="/">
                @if(false)
                    <h1 class="mb-12">{{config('configurazione.tag_title')}}</h1>
                @else
                    <img alt="Logo" src="/loghi/logo-grande.png" class="h-100px mb-12">
                @endif
            </a>
            @yield('content')
        </div>
        <div class="d-flex flex-center flex-column-auto p-10">
            <div class="d-flex align-items-center fw-bold fs-6">
                <a href="mailto:" class="text-muted text-hover-primary px-2">Contattaci</a>
            </div>
        </div>
    </div>
</div>
@include('Backend._layout.modal')

<script src="/assets_backend/plugins/global/plugins.bundle.js"></script>
<script src="/assets_backend/js/scripts.bundle.js"></script>
<script src="/assets_backend/js-miei/mieiScript.js?v=4"></script>

@stack('customScript')
<script>
    $(function () {
        modalAjax();
    });
</script>
</body>
</html>
