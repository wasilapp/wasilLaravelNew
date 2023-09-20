@extends('manager.layouts.app', ['title' => 'Orders'])

@section('css')
@endsection

@section('content')

    <!-- Start Content-->
    <div class="container-fluid">
        <x-alert></x-alert>

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('manager.dashboard')}}">{{env('APP_NAME')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{__('manager.orders')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('manager.orders')}}</h4>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">


                        <div class="float-right">
                            {{ $orders->links() }}
                        </div>
      
                         <div class="float-right m-4 mx-2 " >
                            <ul class="" id="statu">
                                <a href="{{route('manager.orders.index')}}"> <li value="">All</li></a>
                                @for($i=1;$i<6;$i++)
                                     <a href="{{route('manager.status',$i)}}"> <li value="{{$i}}">{{\App\Models\Order::getTextFromStatus($i,2)}}</li></a>
                                @endfor

                            </ul>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{__('manager.order')}} ID</th>
                                    <th>{{__('manager.date')}}</th>
                                    <th>{{__('manager.order_type')}}</th>
                                    <th>{{__('manager.payment_method')}}</th>
                                    <th>{{__('manager.total')}}</th>
                                    <th style="width: 250px;">{{__('manager.order_status')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr href="{{route('manager.orders.edit',['id'=>$order['id']])}}">

                                        <td><span class=" text-body font-weight-bold">#{{$order['id']}}</span></td>
                                        @if($order->orderTime )
                                        <?php $date = strtotime($order->orderTime['order_date']); 
                                                $time = strtotime($order->orderTime['order_time']);  ?>
                                        <td> {{ date('M d Y', $date) }}
                                                <small
                                                class="text-muted"> {{ date('h:i A', $time) }} </small>
                                             </td>
                                        @else
                                        <td> {{\Carbon\Carbon::parse($order['created_at'])->setTimezone(\App\Helpers\AppSetting::$timezone)->format('M d Y')}}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($order['created_at'])->setTimezone(\App\Helpers\AppSetting::$timezone)->format('h:i A')}}</small>
                                        </td>
                                        @endif
                                        <td>{{\App\Models\Order::getTextFromOrderType($order['order_type'])}}</td>
                                        <td>{{\App\Models\Order::getTextFromPaymentType($order['orderPayment']['payment_type'])}}</td>
                                        <td>$ {{round($order['total'], 2)}}</td>
                                        <td>
                                            @if(\App\Models\Order::isCancelStatus($order['status']))
                                                <span
                                                    class="text-danger">{{ \App\Models\Order::getTextFromStatus($order['status'],$order['order_type'])}}</span>
                                            @elseif(\App\Models\Order::isPaymentConfirm($order['status']))
                                                <a href="{{route('manager.orders.edit',['id'=>$order['id']])}}"> {{\App\Models\Order::getTextFromStatus($order['status'],$order['order_type'])}}</a>
                                            @else
                                                <span
                                                    class="text-danger">{{ \App\Models\Order::getTextFromStatus($order['status'],$order['order_type'])}}</span>
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>
                        </div>


                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div>
        </div>
    </div> <!-- container -->

@endsection

@section('script')
@endsection
