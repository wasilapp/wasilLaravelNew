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
                        <div class="col-12">
                            <div class="row mb-2">
                                <div class="col-sm-4">
                                    <h5>{{__('manager.my_delivery_boys')}}</h5>
                                </div>
                            </div>

                          <div class="row">
                               <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                        <h5 class="card-title text-center">{{__('admin.profile_pic')}}</h5>
                            <div class="form-group ">
                                <img  src="{{ asset($delivery_boy->avatar_url) }}"style="width:80%;"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                        <h5 class="card-title text-center">{{__('admin.driving_license')}}</h5>
                            <div class="form-group ">
                                <img src="{{asset($delivery_boy->driving_license)}}" style="width:80%;"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6  col-lg-8 col-xl-9">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">{{__('admin.general_information')}}</h5>

                            {{-- <form action="{{ route('admin.delivery-boy.update', $delivery_boy->id) }}" method="post" enctype="multipart/form-data">
                                @csrf --}}
                                <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="form-group mt-0">
                                        <label for="name">{{__('admin.name')}}</label>
                                        <span>{{$delivery_boy->name}}</span>
                                        {{-- <input  type="text"
                                               class="form-control @if($errors->has('name')) is-invalid @endif"
                                               id="name" placeholder="{{__('admin.name')}}" name="name" value="{{$delivery_boy->name}}" readonly>
                                        @if($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                        @endif --}}
                                    </div>
                                </div>


                                <div class="col-12 col-lg-6">
                                    <div class="form-group mt-0">
                                        <label for="email">{{__('admin.email')}}</label>
                                        <span>{{$delivery_boy->email}}</span>
                                        {{-- <input type="email"
                                               class="form-control @if($errors->has('email')) is-invalid @endif"
                                               id="email" placeholder="{{__('admin.email')}}" name="email" value="{{$delivery_boy->email}}" readonly>
                                        @if($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif --}}
                                    </div>
                                </div>

                            <div class="col-lg-6">
                                    <div class="form-group mt-0">
                                        <label for="email">{{__('admin.mobile')}}</label>
                                        <span>{{$delivery_boy->mobile}}</span>
                                        {{-- <input type="email"
                                               class="form-control @if($errors->has('email')) is-invalid @endif"
                                               id="email" placeholder="{{__('admin.mobile')}}" name="email" value="{{$delivery_boy->mobile}}" readonly>
                                        @if($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif --}}
                                    </div>
                                </div>

                            <div class="col-lg-6">
                                    <div class="form-group mt-0">
                                        <label for="email">{{__('admin.type')}}</label>
                                        <span>@if($delivery_boy->shop_id){{ __('admin.shop_driver') }} || {{$delivery_boy->shop->name}} @else  {{__('admin.freelancer')}} @endif</span>
                                        {{-- <input type="email"
                                               class="form-control @if($errors->has('email')) is-invalid @endif" readonly
                                               id="email" placeholder="{{__('admin.mobile')}}" name="email" value="@if($delivery_boy->shop_id){{ __('admin.shop_driver') }} || {{$delivery_boy->shop->name}} @else  {{__('admin.freelancer')}} @endif" >
                                        @if($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif --}}
                                    </div>
                                </div>


                            <div class="col-lg-6">
                                    <div class="form-group mt-0">
                                        <label for="email">{{__('admin.product_type')}}</label>
                                        <span>@if($delivery_boy->category) {{$delivery_boy->category->title}}@endif</span>
                                        {{-- <input type="email"
                                               class="form-control @if($errors->has('email')) is-invalid @endif" readonly
                                               id="email" placeholder="" name="email" value="@if($delivery_boy->category) {{$delivery_boy->category->title}}@endif" >
                                        @if($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif --}}
                                    </div>
                                </div>
                            </div>

                        {{-- </form> --}}
                        </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-right">
                    @if(!$delivery_boy->is_verified)
                        <a href="{{route('admin.verify',$delivery_boy->id)}}" class="btn btn-success waves-effect waves-light mr-1">{{__('admin.verify')}}
                        </a>
                    @endif
                    {{--  --}}
                </div>
            </div>
                    </div>
            </div>
        </div>



    </div>



@endsection

@section('script')

@endsection
