@extends('admin.layouts.app', ['title' => 'New Banner'])

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
                            <li class="breadcrumb-item"><a href="{{route('admin.users-banners.create')}}">{{__('admin.banners')}}</a></li>
                            <li class="breadcrumb-item active">{{__('admin.edit')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.add_banners')}}</h4>
                    <form class="row" action="{{ route('admin.users-banners.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-center">{{ __('admin.profile_pic') }} <span class="text-danger">*</span></h5>
                                    <div class="form-group">
                                        <input type="file" name="url" id="image" required
                                            data-plugins="dropify" data-default-file="" />
                                        <p class="text-muted text-center mt-2 mb-0">
                                            {{ __('admin.upload_image') }}</p>
                                    </div>
                                </div>
                                <input class="form-check-input" type="text" id="userCheckbox" name="type" value="user" hidden>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{__('admin.create')}}</button>
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
