@extends('admin.layouts.app', ['title' => 'Coupons'])

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
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{env('APP_NAME')}}</a></li>
                            <li class="breadcrumb-item active">{{__('admin.user_wallet')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.user_wallet')}}</h4>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-4">

                                    {{ $coupons->links() }}

                            </div>
                            <div class="col-sm-8">
                                <div class="text-sm-right">
                                    <a type="button" href="{{route('admin.wallet-coupons.create')}}"
                                       class="btn btn-primary waves-effect waves-light mb-2 text-white">{{__('admin.create_coupon')}}
                                    </a>
                                </div>
                            </div><!-- end col-->
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="thead-light">

                                <tr>
                                    <th>{{__('admin.user_name')}}</th>
                                    <th>{{__('admin.status')}}</th>
                                    <th>{{__('admin.remaing')}}</th>
                                    <th>{{__('admin.total')}}</th>
                                    <th>{{__('admin.started_at')}}</th> 
                                    <th style="width: 82px;">{{__('admin.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($coupons as $coupon)
                                    <tr>
                                        <td><span class="font-weight-bold">{{$coupon->user->name}}</span></td>
                                        <td>@if($coupon->status)
                                                <span class="bg-primary mr-1" style="border-radius: 50%;width: 8px;height: 8px;  display: inline-block;"></span>{{__('admin.active')}}
                                            @else
                                                <span class="bg-danger mr-1" style="border-radius: 50%;width: 8px;height: 8px;  display: inline-block;"></span>{{__('admin.deactive')}}

                                            @endif</td>
                                        <td class="" >{{$coupon->price - $coupon->usage}}</td>
                                        <td class="" >{{$coupon->price}}</td>
                                        <td>{{\Carbon\Carbon::parse($coupon->created_at)->setTimezone(\App\Helpers\AppSetting::$timezone)->format('d, M')}}</td>
                                        <td>
                                            <a href="{{route('admin.wallet-coupons.show',['id'=>$coupon->id])}}"
                                               style="font-size: 20px"> <i
                                                    class="mdi mdi-pencil"></i></a>
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
