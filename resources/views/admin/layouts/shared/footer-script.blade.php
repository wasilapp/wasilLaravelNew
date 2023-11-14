<!-- bundle -->
<!-- Vendor js -->
<script src="{{asset('assets/js/vendor.min.js')}}"></script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script><!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>




<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
   <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>
   <script>
       var firebaseConfig = {
        apiKey: "AIzaSyBGQOy6X1kmU4bZPDI0Nmvw1xn9QB-Arvk",
        authDomain: "wasilapp-e7679.firebaseapp.com",
        projectId: "wasilapp-e7679",
        storageBucket: "wasilapp-e7679.appspot.com",
        messagingSenderId: "677303892936",
        appId: "1:677303892936:web:40840db8947775f7e01efd",
        measurementId: "G-44ZFGT8DD6"
       };
       firebase.initializeApp(firebaseConfig);

       //firebase.analytics();
    const messaging = firebase.messaging();
        messaging
    .requestPermission()
    .then(function () {
    //MsgElem.innerHTML = "Notification permission granted."
        console.log("Notification permission granted.");

        // get the token in the form of promise
        return messaging.getToken()

    })
    .then(function(token) {
    // print the token on the HTML page
        console.log(token);
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route("admin.store.token") }}',
                    type: 'POST',
                    data: {
                        token: token,
                        '_token': '{{ csrf_token() }}',
                        '_method': 'POST',
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        console.log('Token stored.');
                    },
                    error: function (error) {
                        console.log(error);
                    },
                });
    })
    .catch(function (err) {
        console.log("Unable to get permission to notify.", err);
    });

       messaging.onMessage(function (payload) {
        //alert(payload);
        $oldUnreadCount = $('#notification #img-notification').attr("data-count");
        console.log('oldUnreadCount' , $oldUnreadCount);
        $('#notification #img-notification').attr("data-count", 1);
        if( $oldUnreadCount == 0 ){
            $('#notification > a').append('\
            <span class="unread-count" id="unread-count" data-count ="1">1</span>\
            ');
            $('#no-notification').css('display','none')

        } else{
            console.log('oldUnreadCount' , $oldUnreadCount);
            $newUnreadCount = Number($oldUnreadCount) + 1;
            console.log('newUnreadCount' , $newUnreadCount);
            $('#notification #img-notification').attr("data-count", $newUnreadCount);
            $('#notification #unread-count').html($newUnreadCount);
        }

        console.log('payload',payload);
        var notify;
        notify = new Notification(payload.notification.title,{
            body: payload.notification.body,
            LinkUrl: payload.notification.LinkUrl,
            icon: payload.notification.icon,
            tag: "Dummy"
        });
        console.log(payload.notification);
        console.log(payload.notification.body);

    console.log('data',payload.notification.created_at);
    //alert(payload.notification.body);
        
                url = '{{ route('admin.getLatestNotifications') }}';
                $.ajax({
                    url: url,
                    type: 'get',
                    processData: false,
                    contentType: false,
                    data: '',
                    beforeSend: function () {
                    },
                    error: function (response) {
                    },
                    success: function (response) {
                        console.log(response);
                        notifications = response.notifications;
                        notifications.forEach(notification => {
                            console.log('notification',notification);
                            bodyEn = notification.data.en;

                            console.log('className.read_at',notification.read_at);
                            var readAt = notification.read_at;
                            if (readAt === null) {
                                className = "notification-unread";
                            } else {
                                className = "";
                            }

                            console.log('className',className);
                           // date = moment([notification.created_at]).fromNow();
                            date = moment(notification.created_at).fromNow();

                            console.log('bodyEn',bodyEn.body);
                            baseUrl = '{{ url("") }}';
                            icon = baseUrl + '/' + notification.data.icon;
                        
                            $('#notification .dropdown-menu .nk-notification ').prepend('\
                            <a href="'+notification.data.url+'?notification_id='+notification.data.id+'" class="dropdown-item notify-item '+className+'"  >\
                                <div class="notify-icon bg-primary">\
                                    <img class="icon-circle" src="'+icon+'" alt="" style="width:40px">\
                                </div>\
                                <p class="notify-details">'+bodyEn.body+'\
                                    <small class="text-muted">'+date+'</small>\
                                </p>\
                            </a>\
                            ');
                        });
                    }
                });


       });
   </script>
























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
                            console.log(response.delivery_count);
                           
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

    submenuToggle.addEventListener('click', function() {
        if (subMenu.style.display === 'block') {
            subMenu.style.display = 'none';
            submenuToggle.classList.remove('active');
        } else {
            subMenu.style.display = 'block';
            submenuToggle.classList.add('active');
        }
    });

    const submenuToggle2 = document.getElementById('submenu-toggle2');
    const subMenu2 = submenuToggle2.querySelector('.sub-menu2');

    submenuToggle2.addEventListener('click', function() {
        if (subMenu2.style.display === 'block') {
            subMenu2.style.display = 'none';
            submenuToggle2.classList.remove('active');
        } else {
            subMenu2.style.display = 'block';
            submenuToggle2.classList.add('active');
        }
    });

    const submenuToggle3 = document.getElementById('submenu-toggle3');
    const subMenu3 = submenuToggle3.querySelector('.sub-menu3');

    submenuToggle3.addEventListener('click', function() {
        if (subMenu3.style.display === 'block') {
            subMenu3.style.display = 'none';
            submenuToggle3.classList.remove('active');
        } else {
            subMenu3.style.display = 'block';
            submenuToggle3.classList.add('active');
        }
    });

    const submenuToggle4 = document.getElementById('submenu-toggle4');
    const subMenu4 = submenuToggle4.querySelector('.sub-menu4');

    submenuToggle4.addEventListener('click', function() {
        if (subMenu4.style.display === 'block') {
            subMenu4.style.display = 'none';
            submenuToggle4.classList.remove('active');
        } else {
            subMenu4.style.display = 'block';
            submenuToggle4.classList.add('active');
        }
    });
    const submenuToggle5 = document.getElementById('submenu-toggle5');
    const subMenu5 = submenuToggle5.querySelector('.sub-menu5');

    submenuToggle5.addEventListener('click', function() {
        if (subMenu5.style.display === 'block') {
            subMenu5.style.display = 'none';
            submenuToggle5.classList.remove('active');
        } else {
            subMenu5.style.display = 'block';
            submenuToggle5.classList.add('active');
        }
    });
    // const submenuToggle2 = document.getElementById('submenu-toggle2');
    // const subMenu2 = submenuToggle2.querySelector('.sub-menu');
    // const menuArrow2 = submenuToggle2.querySelector('.menu-arrow2');

    // submenuToggle2.addEventListener('click', function() {
    //     if (subMenu2.style.display === 'block') {
    //         subMenu2.style.display = 'none';
    //         submenuToggle2.classList.remove('active');
    //     } else {
    //         subMenu2.style.display = 'block';
    //         submenuToggle2.classList.add('active');
    //     }
    // });

    // const submenuToggle3 = document.getElementById('submenu-toggle3');
    // const subMenu3 = submenuToggle3.querySelector('.sub-menu');
    // const menuArrow3 = submenuToggle3.querySelector('.menu-arrow3');

    // submenuToggle3.addEventListener('click', function() {
    //     if (subMenu3.style.display === 'block') {
    //         subMenu3.style.display = 'none';
    //         submenuToggle3.classList.remove('active');
    //     } else {
    //         subMenu3.style.display = 'block';
    //         submenuToggle3.classList.add('active');
    //     }
    // });
    // const submenuToggle4 = document.getElementById('submenu-toggle4');
    // const subMenu4 = submenuToggle3.querySelector('.sub-menu4');
    // const menuArrow4 = submenuToggle3.querySelector('.menu-arrow4');

    // submenuToggle4.addEventListener('click', function() {
    //     if (subMenu4.style.display === 'block') {
    //         subMenu4.style.display = 'none';
    //         submenuToggle4.classList.remove('active');
    //     } else {
    //         subMenu4.style.display = 'block';
    //         submenuToggle4.classList.add('active');
    //     }
    // });

    // const submenuToggle4 = document.getElementById('submenu-toggle4');
    // const subMenu4 = submenuToggle3.querySelector('.sub-menu4');
    // const menuArrow4 = submenuToggle3.querySelector('.menu-arrow4');

    // submenuToggle4.addEventListener('click', function() {
    //     if (subMenu4.style.display === 'block') {
    //         subMenu4.style.display = 'none';
    //         submenuToggle4.classList.remove('active');
    //     } else {
    //         subMenu4.style.display = 'block';
    //         submenuToggle4.classList.add('active');
    //     }
    // });
    </script>
@yield('script-bottom')
