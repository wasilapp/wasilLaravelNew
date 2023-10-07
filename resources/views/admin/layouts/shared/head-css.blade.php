@yield('css')

<!-- icons -->
<link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

<!-------Styles--------->
<link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />

<link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700,900" rel="stylesheet" type="text/css" id="app-default-stylesheet" />
<link href="{{asset('assets/css/app.min.css')}} " rel="stylesheet" type="text/css" id="app-default-stylesheet" />
<link href="{{asset('assets/css/bootstrap-dark.min.css')}} " rel="stylesheet" type="text/css" id="bs-dark-stylesheet" disabled />
<link href="{{asset('assets/css/app-dark.min.css')}} " rel="stylesheet" type="text/css" id="app-dark-stylesheet"  disabled />
<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}"><!-- Toastr CSS -->
<link href="{{asset('assets/css/style.css')}} " rel="stylesheet" type="text/css" id="app-default-stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/css/loading.css') }}">
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

@if(trans('admin.dir') !== 'rtl')
    <link href="{{asset('assets/css/style-en.css')}} " rel="stylesheet" type="text/css" id="app-default-stylesheet" />
@else
    <link href="{{asset('assets/css/style-ar.css')}} " rel="stylesheet" type="text/css" id="app-default-stylesheet" />
@endif
<style>
    .submenu .sub-menu {
        display: none;
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
    }

    .submenu.active .sub-menu {
        display: block;
        opacity: 1;
        transform: translateY(0);
        background: #fff;
        box-shadow: 0px 0px 15px 1px #00000014;
        padding: 10px 0;
    }

    .submenu .menu-arrow {
        transition: transform 0.3s ease-in-out;
    }

    .submenu.active .menu-arrow {
        transform: rotate(90deg);
    }
</style>
