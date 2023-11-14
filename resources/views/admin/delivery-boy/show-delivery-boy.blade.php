@extends('admin.layouts.app', ['title' => 'Manage Delivery boy'])

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
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.delivery-boys.index') }}">{{ __('admin.delivery_boy') }}</a></li>
                            <li class="breadcrumb-item active">{{__('manager.manage')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ $delivery_boy->name }}</h4>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                        <h5>{{__('manager.my_delivery_boys')}}</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-6 col-xl-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                            <h5 class="card-title text-center">{{__('admin.profile_pic')}}</h5>
                                <div class="form-group" style="height: 300px;">
                                    <img  src="{{ asset($delivery_boy->avatar_url) }}" style="width:80%;height: 100%;object-fit: contain;"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                            <h5 class="card-title text-center">{{__('admin.driving_license')}}</h5>
                                <div class="form-group" style="height: 300px;">
                                    <img src="{{asset($delivery_boy->driving_license)}}" style="width:80%;height: 100%;object-fit: contain;"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-12 col-xl-12 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">{{__('admin.general_information')}}</h5>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group mt-0">
                                            <label for="name">{{__('admin.name')}}</label>
                                            <span>{{$delivery_boy->name}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mt-0">
                                            @if ($delivery_boy->is_offline == 0)
                                                <label for="is_offline"><span class="bg-primary mr-1"
                                                    style="border-radius: 50%;width: 8px;height: 8px;  display: inline-block;"></span></label>
                                                <span>Online</span>
                                            @else
                                                <label for="is_offline"><span class="bg-danger mr-1"
                                                    style="border-radius: 50%;width: 8px;height: 8px;  display: inline-block;"></span></label>
                                                <span>Offline</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mt-0">
                                            <label for="agency_name">agency_name</label>
                                            <span>{{$delivery_boy->agency_name}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mt-0">
                                            <label for="email">{{__('admin.email')}}</label>
                                            <span>{{$delivery_boy->email}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mt-0">
                                            <label for="mobile">{{__('admin.mobile')}}</label>
                                            <span>{{$delivery_boy->mobile}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mt-0">
                                            <label for="car_number">car_number</label>
                                            <span>{{$delivery_boy->car_number}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mt-0">
                                            <label for="email">{{__('admin.type')}}</label>
                                            <span>@if($delivery_boy->shop_id){{ __('admin.shop_driver') }} || {{$delivery_boy->shop->name}} @else  {{__('admin.freelancer')}} @endif</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mt-0">
                                            <label for="email">{{__('admin.product_type')}}</label>
                                            <span>@if($delivery_boy->category) {{$delivery_boy->category->title}}@endif</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mt-0">
                                            @if ($delivery_boy->category_id == 1)
                                                <label for="full_gas_bottles">Full bottles</label>
                                                <span>{{$delivery_boy->full_gas_bottles}}</span>
                                            @else
                                                <label for="full_gas_bottles">Full gas</label>
                                                <span>{{$delivery_boy->full_gas_bottles}}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mt-0">
                                            @if ($delivery_boy->category_id == 1)
                                                <label for="empty_gas_bottles">Empty bottles</label>
                                                <span>{{$delivery_boy->empty_gas_bottles}}</span>
                                            @else
                                                <label for="empty_gas_bottles">Empty gas</label>
                                                <span>{{$delivery_boy->empty_gas_bottles}}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group mt-0">
                                            @if ($delivery_boy->category_id == 1)
                                                <label for="empty_gas_bottles">bottles capacity</label>
                                                <span>{{$delivery_boy->gas_bottles_capacity}}</span>
                                            @else
                                                <label for="empty_gas_bottles">gas capacity</label>
                                                <span>{{$delivery_boy->gas_bottles_capacity}}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

@endsection
