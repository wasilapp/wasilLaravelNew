<!DOCTYPE html>
<html lang="en">

<head>
    @include('manager.layouts.shared/title-meta', ['title' => $title])
    @include('manager.layouts.shared/head-css')
    {{--@include('layouts.shared/head-css', ["demo" => "dark"])--}}
</head>

<body @yield('body-extra')>
<!-- Begin page -->
<div id="wrapper">
    @include('manager.layouts.shared/topbar')

    @include('manager.layouts.shared/left-sidebar')


    <div class="content-page">
        <div class="content">
            @yield('content')
        </div>
        <!-- content -->

        @include('manager.layouts.shared/footer')

    </div>
</div>
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
<!-- END wrapper -->

@include('manager.layouts.shared.right-sidebar')


@include('manager.layouts.shared/footer-script')

</body>
</html>
