<html lang="en">
<head>

    <!-- icons -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" id="bs-default-stylesheet"/>
    <link href="{{asset('assets/css/app.min.css')}} " rel="stylesheet" type="text/css" id="app-default-stylesheet"/>
    <title>Order Receipt</title>
</head>
<body class="p-2">
<!-- Start Content-->
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">

                <h4 class="page-title">Order #{{$order['id']}} </h4>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Products</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered table-centered mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>Product Image</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                            </thead>
                            <tbody>
           
                            <tr>
                                <th scope="row" colspan="3" class="text-right">{{__('manager.sub_total')}}</th>
                                <td>
                                    <div
                                        class="font-weight-bold">{{\App\Helpers\AppSetting::$currencySign}} {{$order['order']}}</div>
                                </td>
                            </tr>

                            @if($order['coupon_discount'])
                                <tr>
                                    <th scope="row" colspan="3"
                                        class="text-right">{{__('manager.coupon_discount')}}</th>
                                    <td>
                                        <div>
                                            -{{\App\Helpers\AppSetting::$currencySign}} {{$order['coupon_discount']}}</div>
                                    </td>
                                </tr>
                            @endif


                            <tr>
                                <th scope="row" colspan="3" class="text-right">{{__('manager.delivery_fee')}}</th>
                                <td>{{\App\Helpers\AppSetting::$currencySign}} {{$order['delivery_fee']}}</td>
                            </tr>
                             
                            <tr>
                                <th scope="row" colspan="3" class="text-right">{{__('manager.total')}}</th>
                                <td>
                                    <div
                                        class="font-weight-bold">{{\App\Helpers\AppSetting::$currencySign}} {{$order['total']}}</div>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">{{__('manager.shipping_information')}}</h4>
                            <h5 class="font-family-primary font-weight-semibold">{{$order['user']['name']}}</h5>
                            @if(\App\Models\Order::isOrderTypePickup($order['order_type']))
                                <p class="mb-2"><span
                                        class="font-weight-semibold mr-2">{{\App\Models\Order::getTextFromOrderType($order['order_type'])}}
                                </p>
                            @else
                                <p class="mb-1"><span
                                        class="font-weight-semibold mr-2">{{__('manager.address')}}:</span>{{$order['address']['address']}} {{$order['address']['city']}} {{$order['address']['pincode']}}
                                </p>
                            @endif
                            <p class="mb-0"><span
                                    class="font-weight-semibold mr-2">{{__('manager.email')}}:</span> {{$order['user']['email']}}
                            </p>

                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">{{__('manager.billing_information')}}</h4>

                            <ul class="list-unstyled mb-0">
                                <li>
                                    <p class="mb-0"><span
                                            class="font-weight-semibold mr-2">{{__('manager.payment_type')}}:</span> {{\App\Models\Order::getTextFromPaymentType($order['order_payment']['payment_type'])}}
                                    </p>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>

                @if(!\App\Models\Order::isOrderTypePickup($order['order_type']))
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title mb-3">{{__('manager.delivery_boy')}}</h4>
                                @if($order['delivery_boy'])
                                    <div class="text-center">
                                        <img src="{{asset('/storage/'.$order['delivery_boy']['avatar_url'])}}"
                                             class="img-fluid rounded-circle"
                                             alt="" height="44px" width="44px"/>
                                        <h5><b>{{$order['delivery_boy']['name']}}</b></h5>
                                        <p class="mb-1"><span
                                                class="font-weight-semibold">{{__('manager.email')}} :</span> {{$order['delivery_boy']['email']}}
                                        </p>
                                        <p class="mb-0"><span
                                                class="font-weight-semibold">{{__('manager.phone')}} :</span> {{$order['delivery_boy']['mobile']}}
                                        </p>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <h5>{{__('manager.first_assign_delivery_boy')}}</h5>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div> <!-- end col -->
                @endif
            </div>
        </div>
    </div>
</div>
</body>
</html>
