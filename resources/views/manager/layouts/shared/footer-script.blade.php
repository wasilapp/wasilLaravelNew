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
                url: "{{ url('manager/getorder') }}",
                method: 'GET', //Get method,
                dataType: "json",
                success: function(response) {
                    order_count = localStorage.getItem("order_count");
                  
                    if (response.order_count != 0) {
                         if (localStorage.getItem("order_count") < response.order_count) {
                            localStorage.setItem("order_count", response.order_count);
                            jQuery("#order-modal").modal('show'); 
                        }else{
                            localStorage.setItem("order_count", response.order_count);

                        }
                   
                    } else {
                        localStorage.setItem("order_count", response.order_count);
                    }
                   
              
                    setTimeout(noti, 20000);
                }
            });
        })();
    </script>
@yield('script-bottom')
