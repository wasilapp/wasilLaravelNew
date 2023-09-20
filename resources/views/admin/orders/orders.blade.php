@extends('admin.layouts.app', ['title' => 'Orders'])

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
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{env('APP_NAME')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{__('admin.orders')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.orders')}}</h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <h5 class=" col-12">filter by status</h5>
                            <div class="float-right col-12" >
                                <ul class="" id="statu">
                                    <a href="{{route('admin.orders.index')}}" class="{{ Route::currentRouteName() == 'admin.orders.index' ? 'active' : ''}}"> <li value="">All</li></a>
                                    @for($i=1;$i<6;$i++)
                                        <a href="{{route('admin.orders.status',$i)}}" class="{{ Request::is('*'.$i.'*') ? 'active' : ''}}"> 
                                        <li value="{{$i}}">{{\App\Models\Order::getTextFromStatus($i,2)}}</li>
                                        </a>
                                    @endfor
                                </ul>
                            </div>
                        </div>
                        <div class="row">
                            <h5 class=" col-12">filter by category</h5>
                            <div class="float-right m-4 " >
                                <form action={{route('admin.orders.index')}} method="get">
                                    @csrf
                                    <select name='search' id="search" class="form-control">
                                        <option>select category</option>
                                        <option value="all">All</option>
                                        @foreach($cats as $cat)
                                            <option value="{{$cat->id}}">{{$cat->title}}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="float-right">
                            {{ $orders->links() }}
                            </div>
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{__('admin.order')}} ID</th>
                                    <th>{{__('admin.date')}}</th>
                                    <th>{{__('admin.order_type')}}</th>
                                    <th>{{__('admin.shop')}}</th>
                                    <th>{{__('admin.category')}}</th>
                                    <th>{{__('admin.payment_method')}}</th>
                                    <th>{{__('admin.total')}}</th>
                                    <th style="width: 250px;">{{__('admin.order_status')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td><span class=" text-body font-weight-bold">#{{$order['id']}}</span></td>
                                        <td> {{\Carbon\Carbon::parse($order['created_at'])->format('M d Y')}}
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($order['created_at'])->format('h:i A')}}</small>
                                        </td>
                                        <td>{{\App\Models\Order::getTextFromOrderType($order['order_type'])}}</td>
                                        <td> {{$order['shop'] ? $order['shop']['name']: ''}} </td>
                                        <td>{{ $order['shop']? $order['shop']['category']['title'] : $order['deliveryBoy']['category']['title']}}</td>
                                        <td>{{\App\Models\Order::getTextFromPaymentType($order['orderPayment']['payment_type'])}}</td>
                                        <td>$  {{round($order['total'], 2)}}</td>
                                        <td>
                                            @if(\App\Models\Order::isPaymentConfirm($order['status']))
                                                <a href="{{route('admin.orders.show',$order['id'])}}"><span class="text-primary">{{\App\Models\Order::getTextFromStatus($order['status'],$order['order_type'])}}</span></a>
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
<script>
    $('#search').on('change',function(){
        $('form').submit()
    })
</script>

@endsection
