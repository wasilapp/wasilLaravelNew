<!DOCTYPE html>
<html lang="{{ App::getLocale() }}" dir="{{ trans('admin.dir')}}" class="js">

<head>
    @include('admin.layouts.shared/title-meta', ['title' => $title])
    @include('admin.layouts.shared/head-css')
</head>

<body @yield('body-extra')>
<!-- Begin page -->
<div id="wrapper">
    @include('admin.layouts.shared/topbar')

    @include('admin.layouts.shared/left-sidebar')


    <div class="content-page">
        <div class="content" >
            @yield('content')
        </div>
        <!-- content -->

        @include('admin.layouts.shared/footer')

    </div>
</div>

<!-- شاشة التحميل -->
<div class="loading-screen" id="loader">
    <div class="spinner-box">
        <div class="blue-orbit leo">
        </div>

        <div class="green-orbit leo">
        </div>

        <div class="red-orbit leo">
        </div>

        <div class="white-orbit w1 leo">
        </div><div class="white-orbit w2 leo">
        </div><div class="white-orbit w3 leo">
        </div>
    </div>
</div>

<!-- نهاية شاشة التحميل -->

<div class="modal fade" id="order-modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-notify modal-info" role="document">
        <div class="modal-content text-center">
            <div class="modal-header d-flex justify-content-center">
                <p class="heading">{{ trans('admin.be_up_to_date') }}</p>
            </div>
            <div class="modal-body"><i class="fa fa-bell fa-4x animated rotateIn mb-4"></i>
                <p>{{ trans('admin.new_order_arrive') }}</p>
            </div>
            <div class="modal-footer flex-center">
                <a role="button" class="btn btn-outline-secondary-modal waves-effect"
                    onClick="window.location.reload();"
                    data-bs-dismiss="modal">{{ trans('admin.okay') }}</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delivery-modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-notify modal-info" role="document">
        <div class="modal-content text-center">
            <div class="modal-header d-flex justify-content-center">
                <p class="heading">{{ trans('admin.be_up_to_date') }}</p>
            </div>
            <div class="modal-body"><i class="fa fa-bell fa-4x animated rotateIn mb-4"></i>
                <p>{{ trans('admin.New Delivery Boy needs acception')}}</p>
            </div>
            <div class="modal-footer flex-center">
                <a role="button" class="btn btn-outline-secondary-modal waves-effect"
                    onClick="window.location.reload();"
                    data-bs-dismiss="modal">{{ trans('admin.okay') }}</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="shop-modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-notify modal-info" role="document">
        <div class="modal-content text-center">
            <div class="modal-header d-flex justify-content-center">
                <p class="heading">{{ trans('admin.be_up_to_date') }}</p>
            </div>
            <div class="modal-body"><i class="fa fa-bell fa-4x animated rotateIn mb-4"></i>
                <p>{{ trans('admin.New Shop needs acception')}}</p>
            </div>
            <div class="modal-footer flex-center">
                <a role="button" class="btn btn-outline-secondary-modal waves-effect"
                    onClick="window.location.reload();"
                    data-bs-dismiss="modal">{{ trans('admin.okay') }}</a>
            </div>
        </div>
    </div>
</div>
<!-- END wrapper -->

@include('admin.layouts.shared.right-sidebar')


@include('admin.layouts.shared/footer-script')

<script>
    window.onload = function() {
        document.querySelector('.loading-screen').style.display = 'flex';
        setTimeout(function() {
            document.querySelector('.loading-screen').style.display = 'none';
            document.querySelector('.content-page').style.display = 'block';
        }, 500);
    };
</script>
</body>
</html>
