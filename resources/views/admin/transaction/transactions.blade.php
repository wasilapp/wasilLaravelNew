@extends('admin.layouts.app', ['title' => 'Transactions'])

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
                            <li class="breadcrumb-item active">{{__('admin.transactions')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.transactions')}}</h4>
                </div>
            </div>
        </div>


  
        <div class="card">
            <div class="card-body">
                @if($shops->count()>0)
                    <h4>{{__('admin.shop_transaction')}}</h4>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap table-hover mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{__('admin.shop_id')}}</th>
                                        <th>{{__('admin.shop_name')}}</th>
                                        <th>{{__('admin.total_amount')}}</th>
                                        <th>{{__('admin.paid_amount')}}</th>
                                        <th>{{__('admin.remaining_amount')}}</th>
                                        <th style="width: 82px;">{{__('admin.action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($shops as $shop)
                                        <tr>
                                            <td>
                                                <a href="{{route('admin.shops.show',['id'=>$shop['id']])}}"
                                                   class="font-weight-semibold"># {{$shop['id']}}</a>
                                            </td>
                                            <td><a href="{{route('admin.shops.show',['id'=>$shop['id']])}}"
                                                   class="font-weight-semibold">{{$shop['name']}}</a>
                                            </td>
                                            <td>
                                                 {{\App\Helpers\CurrencyUtil::doubleToString($shop->orders->whereIn('status',[5,6])->sum('admin_revenue'))}} {{\App\Helpers\AppSetting::$currencySign}}
                                            </td>
                                            <td>
                                                 {{\App\Helpers\CurrencyUtil::doubleToString(\App\Models\Shop::total_shop_to_admin($shop->id))}}   {{\App\Helpers\AppSetting::$currencySign}} 
                                            </td>
                                           <td>{{\App\Helpers\CurrencyUtil::doubleToString(($shop->orders->whereIn('status',[5,6])->sum('admin_revenue'))- (\App\Models\Shop::total_shop_to_admin($shop->id)))}} {{\App\Helpers\AppSetting::$currencySign}} </td>
                                            <td>
                                                <a href="{{route('admin.transactions.create',$shop->id)}}" >{{__('admin.add Payment')}}</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body">
                            <h3>{{__('admin.there_is_no_any_revenues_yet')}}</h3>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @if($deliveryBoys->count()>0)
                    <h4>{{__('admin.delivery_boy_transaction')}}</h4>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap table-hover mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('admin.delivery_boy')}}</th>
                                        <th>{{__('admin.total_amount')}}</th>
                                        <th>{{__('admin.paid_amount')}}</th>
                                        <th>{{__('admin.remaining_amount')}}</th>
                                        <th style="width: 82px;">{{__('admin.action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($deliveryBoys as $deliveryBoy)
                                        <tr>
                                            <td>
                                                <a href="{{route('admin.delivery-boy.show',['id'=>$deliveryBoy['id']])}}"
                                                   class="font-weight-semibold"># {{$deliveryBoy['id']}}</a>
                                            </td>
                                            <td><a href="{{route('admin.delivery-boy.show',['id'=>$deliveryBoy['id']])}}"
                                                   class="font-weight-semibold">{{$deliveryBoy['name']}}</a>
                                            </td>
                                            <td>
                                                 {{\App\Helpers\CurrencyUtil::doubleToString($deliveryBoy->orders->whereIn('status',[5,6])->sum('admin_revenue'))}} {{\App\Helpers\AppSetting::$currencySign}}
                                            </td>
                                            <td>
                                                 {{\App\Helpers\CurrencyUtil::doubleToString(\App\Models\DeliveryBoy::total_shop_to_admin($deliveryBoy->id))}}   {{\App\Helpers\AppSetting::$currencySign}} 
                                            </td>
                                            <td>{{\App\Helpers\CurrencyUtil::doubleToString(($deliveryBoy->orders->whereIn('status',[5,6])->sum('admin_revenue')) - (\App\Models\DeliveryBoy::total_shop_to_admin($deliveryBoy->id)))}} {{\App\Helpers\AppSetting::$currencySign}} </td>
                                            <td>
                                                <a href="{{route('admin.transactions.add_delivery_transaction',$deliveryBoy->id)}}" >{{__('admin.add Payment')}}</a>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body">
                            <h3>{{__('admin.there_is_no_any_revenues_yet')}}</h3>
                        </div>
                    </div>
                @endif
            </div>
        </div>


    </div>

@endsection

@section('script')

@endsection
