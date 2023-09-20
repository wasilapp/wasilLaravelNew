@extends('manager.layouts.app', ['title' => 'Transactions'])

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
                                    </tr>
                                    </thead>
                                    <tbody>
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
                                          
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
    
            </div>
        </div>
    <div class="card">
            <div class="card-body">
                @if($transactions->count()>0)
                    <div class="row justify-content-between mx-1">
                        <h4>{{__('admin.payment')}}</h4>
                        
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-centered table-bordered  table-nowrap table-hover mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">{{__('admin.amount')}}</th>
                                        <th class="text-center">{{__('admin.from_date')}}</th>
                                        <th class="text-center">{{__('admin.to_date')}}</th>
                                        <th class="text-center">{{__('admin.status')}}</th>
                                    </tr>

                                    </thead>
                                   <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td class="text-center">
                                                <a 
                                                   class="font-weight-semibold"># {{ $transaction->id }}</a>
                                            </td>
                                            <td class="text-center">
                                                {{\App\Helpers\AppSetting::$currencySign}} {{\App\Helpers\CurrencyUtil::doubleToString($transaction->total)}}
                                            </td>

                                            <td class="text-center">{{date('D' , strtotime($transaction->from_date))}} | {{date('Y-m-d' , strtotime($transaction->from_date))}}</td>
                                            <td class="text-center">{{date('D' , strtotime($transaction->to_date))}} | {{date('Y-m-d' , strtotime($transaction->to_date))}}</td>
                                            <td class="text-center"> {{__('admin.'.$transaction->status)}}</td>
     
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
