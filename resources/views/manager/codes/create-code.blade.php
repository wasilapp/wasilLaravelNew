@extends('manager.layouts.app', ['title' => 'New code'])

@section('css')
    <link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/summernote/summernote.min.css')}}" rel="stylesheet" type="text/css"/>
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
                                    href="{{route('manager.codes.index')}}">{{__('manager.code')}}</a></li>
                            <li class="breadcrumb-item active">{{__('manager.create')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('manager.new_code')}}</h4>
                </div>
            </div>
        </div>

        <form action="{{route('manager.codes.store')}}" method="post" enctype="multipart/form-data"
              id="code-form">
            @csrf
            <div class="row">

                <div class="col-lg-6">
                    <!-- end col-->

                    <div class="card-box">
                        <h5 class="text-uppercase bg-light p-2 mt-0 mb-3">{{__('manager.general')}}</h5>

                        <div class="form-group mb-3">
                            <label for="title">{{__('manager.code_name')}} <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title"
                                   class="form-control @if($errors->has('title')) is-invalid @endif"
                                    value="{{old('title')}}">
                            @if($errors->has('title'))
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="max_use">{{__('manager.max_use')}} <span class="text-danger">*</span></label>
                            <input type="text" id="max_use" name="max_use"
                                   class="form-control @if($errors->has('max_use')) is-invalid @endif"
                                    value="{{old('max_use')}}">
                            @if($errors->has('max_use'))
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('max_use') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mb-3">
                            <label for="user">{{__('manager.user')}} <span class="text-danger">*</span></label>
                            <select class="form-control"  name="user" id="user" required>
                                <option disabled>Select User</option>
                                @foreach($users as $user)

                                <option value="{{$user->id}}">{{$user->email}}</option>

                                @endforeach
                            </select>

                        </div>



                    </div>

                    <!-- end card-box -->

                </div>





                <div class="col-12">
                    <div class="text-right mb-3">
                        <a href="{{route('manager.codes.index')}}"
                           class="btn w-sm btn-light waves-effect">{{__('manager.cancel')}}</a>
                           <button type="submit" class="btn btn-success waves-effect waves-light">{{__('admin.create')}}</button>

                </div> <!-- end col-->
            </div>
        </form>

    </div>

@endsection

