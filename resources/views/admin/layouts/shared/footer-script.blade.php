<!-- bundle -->
<!-- Vendor js -->
<script src="{{asset('assets/js/vendor.min.js')}}"></script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script><!-- Toastr JS -->

@yield('script')
<!-- App js -->
<script src="{{asset('assets/js/app.min.js')}}"></script>
   <script type="text/javascript">

        let are_you_sure = "{{ trans('messages.are_you_sure') }}";
        let yes = "{{ trans('messages.yes') }}";
        let no = "{{ trans('messages.no') }}";
        let wrong = "{{ trans('messages.wrong') }}";
        let cannot_delete = "{{ trans('messages.cannot_delete') }}";
        let last_image = "{{ trans('messages.last_image') }}";
        let record_safe = "{{ trans('messages.record_safe') }}";
        let select = "{{ trans('labels.select') }}";
        let variation = "{{ trans('labels.variation') }}";
        let enter_variation = "{{ trans('labels.variation') }}";
        let product_price = "{{ trans('labels.product_price') }}";
        let enter_product_price = "{{ trans('labels.product_price') }}";
        let sale_price = "{{ trans('labels.sale_price') }}";
        let enter_sale_price = "{{ trans('labels.sale_price') }}";

        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        @if (Session::has('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (Session::has('error'))
            toastr.error("{{ session('error') }}");
        @endif
        var noticount = 0;
        (function noti() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('admin/getorder') }}",
                method: 'GET', //Get method,
                dataType: "json",
                success: function(response) {
                    order_count = localStorage.getItem("order_count");
                    shop_count = localStorage.getItem("shop_count");
                    delivery_count = localStorage.getItem("delivery_count");


                    // if (response > 9) {
                    //     $('#notificationcount').text(response + "+");
                    // } else {
                    //     $('#notificationcount').text(response);
                    // }
                    if (response.order_count != 0) {
                        if (localStorage.getItem("order_count") < response.order_count) {
                            localStorage.setItem("order_count", response.order_count);
                            jQuery("#order-modal").modal('show');
                        }else{
                            localStorage.setItem("order_count", response.order_count);

                        }

                    } else {
                        localStorage.setItem("order_count", response.delivery_count);
                    }
                      if (response.shop_count != 0) {
                        if (localStorage.getItem("shop_count") <  response.shop_count) {
                            localStorage.setItem("shop_count", response.shop_count);
                            jQuery("#shop-modal").modal('show');
                        }else{
                          localStorage.setItem("shop_count", response.shop_count);

                        }

                      }else {
                        localStorage.setItem("shop_count", response.shop_count);
                    }
                    if (response.delivery_count != 0) {
                        if (localStorage.getItem("delivery_count") < response.delivery_count) {
                            localStorage.setItem("delivery_count", response.delivery_count);
                            jQuery("#delivery-modal").modal('show');
                        }
                        else{
                           localStorage.setItem("delivery_count", response.delivery_count);
                        }
                    } else {
                        localStorage.setItem("delivery_count", response.delivery_count);

                    }
                    setTimeout(noti, 20000);
                }
            });
        })();


    </script>
    <script>
    const submenuToggle = document.getElementById('submenu-toggle');
    const subMenu = submenuToggle.querySelector('.sub-menu');
    const menuArrow = submenuToggle.querySelector('.menu-arrow');

    submenuToggle.addEventListener('click', function() {
        if (subMenu.style.display === 'block') {
            subMenu.style.display = 'none';
            submenuToggle.classList.remove('active');
        } else {
            subMenu.style.display = 'block';
            submenuToggle.classList.add('active');
        }
    });
    </script>
@yield('script-bottom')
