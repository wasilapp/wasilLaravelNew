@extends('admin.layouts.app', ['title' => 'New Delivery Boy'])

@section('css')
    <link href="{{ asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ env('APP_NAME') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.delivery-boys.index') }}">{{ __('admin.delivery_boy') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('admin.edit') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('admin.edit_delivery_boy') }}</h4>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.delivery-boy.update',['id'=>$delivery_boy->id])}}" method="post" enctype="multipart/form-data">
                            @csrf
                            {{method_field('PATCH')}}
                            <div class="row">
                                <div class="col-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title text-center">{{ __('admin.profile_pic') }} <span class="text-danger">*</span></h5>
                                            <div class="form-group">

                                                <input type="file" name="avatar_url" id="image"
                                                    data-plugins="dropify" data-default-file="{{ asset($delivery_boy['avatar_url']) }}" />
                                                <p class="text-muted text-center mt-2 mb-0">
                                                    {{ __('admin.upload_image') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title text-center">{{ __('admin.driving_license') }} <span class="text-danger">*</span>
                                            </h5>
                                            <div class="form-group">
                                                <input type="file" name="driving_license" id="image"
                                                    data-plugins="dropify" data-default-file="{{ asset($delivery_boy['driving_license']) }}" />
                                                <p class="text-muted text-center mt-2 mb-0">
                                                    {{ __('admin.upload_image') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 ">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title text-center">
                                                {{ __('admin.general_information') }}</h5>
                                            <div class="row">
                                                <div class="row col-12">
                                                    <div class="form-group mt-0 col-md-6">
                                                        <label for="name">{{__('admin.enTitle')}}</label>
                                                        <input type="text" class="form-control @if($errors->has('name.en')) is-invalid @endif" id="nameEn" name="name[en]" value="{{$delivery_boy->getTranslation('name','en');}}">
                                                        @if($errors->has('name.en'))
                                                            <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('name.en') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="form-group mt-0 col-md-6">
                                                        <label for="name">{{__('admin.arTitle')}}</label>
                                                        <input type="text" class="form-control @if($errors->has('name.ar')) is-invalid @endif" id="nameeAr" name="name[ar]" value="{{$delivery_boy->getTranslation('name','en');}}">
                                                        @if($errors->has('name.ar'))
                                                            <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('name.ar') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <div class="form-group mt-0">
                                                        <label for="email">{{ __('admin.email') }} </label>
                                                        <input type="email"
                                                            class="form-control @if ($errors->has('email')) is-invalid @endif"
                                                            id="email" placeholder="{{ __('admin.email') }}"
                                                            name="email" value="{{$delivery_boy->email}}" >
                                                        @if ($errors->has('email'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('email') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <div class="form-group mt-0">
                                                        <label for="mobile">{{ __('admin.mobile') }} <span class="text-danger">*</span></label>
                                                            <input type="text"
                                                            class="form-control"
                                                            placeholder="962"
                                                            value="962" disabled >
                                                        <input type="text"
                                                            class="form-control @if ($errors->has('mobile')) is-invalid @endif"
                                                            id="mobile"
                                                            name="mobile" value="{{$delivery_boy->mobile}}" minlength="9" maxlength="9" >
                                                        @if ($errors->has('mobile'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('mobile') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <div class="form-group mt-0">
                                                        <label for="car_number">{{ __('admin.car_number') }} <span class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @if ($errors->has('car_number')) is-invalid @endif"
                                                            id="car_number"
                                                            name="car_number" value="{{$delivery_boy->car_number}}" >
                                                        @if ($errors->has('car_number'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('car_number') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <div class="form-group mt-0">
                                                        <label for="distance">{{ __('admin.distance') }} <span class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @if ($errors->has('distance')) is-invalid @endif"
                                                            id="distance"
                                                            name="distance" value="{{$delivery_boy->distance}}" >
                                                        @if ($errors->has('distance'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('distance') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-6">
                                                    <div class="form-group mt-0">
                                                        <label for="full_gas_bottles">{{ __('admin.full_gas_bottles') }} <span class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @if ($errors->has('full_gas_bottles')) is-invalid @endif"
                                                            id="full_gas_bottles"
                                                            name="full_gas_bottles" value="{{$delivery_boy->full_gas_bottles}}" >
                                                        @if ($errors->has('full_gas_bottles'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('full_gas_bottles') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 text-right">
                                    <button type="submit"
                                        class="btn btn-success waves-effect waves-light mr-1">{{ __('admin.save') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropify/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>
    <script>
        $('#category').on('change',function(){
            id = $('#category').val();
                $.ajax({
               type:'GET',
                url:'/api/v1/user/categories/'+id+'/shops',
                   success:function(data) {
                     console.log('data',data);
                       $('#shop').empty();
                        $("#shop").append('<option value="">--Select Area--</option>');
                        if(data)
                        {
                            data.forEach((e) => {
                                $('#shop').append('<option value="'+e.id+'">'+ e.name+' </option>');
                            });
                        }

                   }
            });
        });
    </script>
@endsection
