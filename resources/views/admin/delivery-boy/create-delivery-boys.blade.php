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
                            <li class="breadcrumb-item active">{{ __('admin.create') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('admin.create_delivery_boy') }}</h4>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.delivery-boy.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title text-center">{{ __('admin.profile_pic') }} <span class="text-danger">*</span></h5>
                                            <div class="form-group">

                                                <input type="file" name="avatar_url" id="image"
                                                    data-plugins="dropify" data-default-file="" />
                                                <p class="text-muted text-center mt-2 mb-0">
                                                    {{ __('admin.upload_image') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title text-center">{{ __('admin.driving_license') }} <span class="text-danger">*</span>
                                            </h5>
                                            <div class="form-group">
                                                <input type="file" name="driving_license" id="image"
                                                    data-plugins="dropify" data-default-file="" />
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
                                                                <input type="text" class="form-control @if($errors->has('name.en')) is-invalid @endif" id="nameEn" name="name[en]" value="{{old('name[en]')}}">
                                                                @if($errors->has('name.en'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $errors->first('name.en') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="form-group mt-0 col-md-6">
                                                                <label for="name">{{__('admin.arTitle')}}</label>
                                                                <input type="text" class="form-control @if($errors->has('name.ar')) is-invalid @endif" id="nameeAr" name="name[ar]" value="{{old('name[ar]')}}">
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
                                                                    name="email" value="{{ old('email') }}" >
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
                                                                    placeholder="+962"
                                                                    value="+962" disabled >
                                                                <input type="text"
                                                                    class="form-control @if ($errors->has('mobile')) is-invalid @endif"
                                                                    id="mobile"
                                                                    name="mobile" value="{{ old('mobile') }}" minlength="9" maxlength="9" >
                                                                @if ($errors->has('mobile'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('mobile') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                                   <div class="col-12 col-lg-6">
                                                            <div class="form-group mt-0">
                                                                <label for="password">{{ __('admin.password') }} <span class="text-danger">*</span></label>
                                                                <input type="password"
                                                                    class="form-control @if ($errors->has('password')) is-invalid @endif"
                                                                    id="password" placeholder="{{ __('admin.password') }}"
                                                                    name="password" value="{{ old('password') }}" >
                                                                @if ($errors->has('password'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('password') }}</strong>
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
                                                                    name="car_number" value="{{ old('car_number') }}" >
                                                                @if ($errors->has('car_number'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('car_number') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>


                                                    <div class="col-12 col-lg-6">
                                                <div class="form-group mt-0">
                                                    <label for="category">{{__('manager.category')}} <span class="text-danger">*</span></label>
                                                    <select class="form-control" name="category_id" id="category"  required>
                                                        <option disabled>{{ trans('admin.SelectCategory') }}</option>
                                                        @foreach($categories as $category)
                                                            <option @if(old('category_id') == $category->id) selected @endif value="{{$category->id}}">{{$category->title}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group mt-0">
                                                        <label for="shop">{{__('admin.shops')}} ** for free driver don't select any shop </label>
                                                        <select class="form-control" name="shop_id" id="shop">
                                                            <option value =''>{{ trans('admin.SelectCategory') }}</option>

                                                        </select>
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
