@extends('admin.layouts.app', ['title' => 'Banner'])

@section('css')
    <link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css"/>
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
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{env('APP_NAME')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.banners.create')}}">{{__('admin.banners')}}</a></li>
                            <li class="breadcrumb-item active">{{__('admin.edit')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.add_banners')}}</h4>
                    <form class="row" action="{{ route('admin.banners.store') }}" method="post" enctype="multipart/form-data">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center">{{ __('admin.profile_pic') }} <span class="text-danger">*</span></h5>
                                    <div class="form-group">
                                        <input type="file" name="avatar_url" id="image" required
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
                                    <h5 class="card-title text-center">Type<span class="text-danger">*</span></h5>
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="userCheckbox" name="type[]" value="user">
                                            <label class="form-check-label" for="userCheckbox">
                                                User
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="shopCheckbox" name="type[]" value="shop">
                                            <label class="form-check-label" for="shopCheckbox">
                                                Shop
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="driverCheckbox" name="type[]" value="driver">
                                            <label class="form-check-label" for="driverCheckbox">
                                                Driver
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
    <script src="{{ asset('assets/libs/dropify/dropify.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-fileuploads.init.js') }}"></script>
@endsection
