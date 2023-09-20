@extends('admin.layouts.app', ['title' => 'New Category'])

@section('css')
    <link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{env('APP_NAME')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.categories.index')}}">{{__('admin.services')}}</a></li>
                            <li class="breadcrumb-item active">{{__('admin.create')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.services')}}</h4>
                </div>
            </div>
        </div>


        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.categories.store')}}" method="post" enctype="multipart/form-data" >
                            @csrf
                            {{-- @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif --}}
                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title">{{__('admin.enTitle')}}</label>
                                    <input type="text" class="form-control @if($errors->has('title.en')) is-invalid @endif" id="titleEn" name="title[en]" value="{{old('title[en]')}}">
                                    @if($errors->has('title.en'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title.en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title">{{__('admin.arTitle')}}</label>
                                    <input type="text" class="form-control @if($errors->has('title.ar')) is-invalid @endif" id="titleAr" name="title[ar]" value="{{old('title[ar]')}}">
                                    @if($errors->has('title.ar'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title.ar') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group mt-0">
                                <label for="type">{{__('admin.type')}}</label>
                                <select required class="form-control @if($errors->has('title')) is-invalid @endif" id="type" name="type" >
                                    <option value="water">{{__('admin.Water')}}</option>
                                    <option value="gas">{{__('admin.Gas')}}</option>
                                </select>
                                @if($errors->has('type'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                @endif
                            </div>


                            <div class="form-group mt-0">
                                <label for="commesion">{{__('admin.commesion')}}</label>
                                <input type="number" step="0.01" class="form-control @if($errors->has('commesion')) is-invalid @endif" id="commesion" name="commesion" value="{{old('commesion')}}">
                                @if($errors->has('commesion'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('commesion') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mt-0">
                                <label for="delivery_fee">{{__('admin.delivery_fee')}}</label>
                                <input  type="number" step="0.01" class="form-control @if($errors->has('delivery_fee')) is-invalid @endif" id="delivery_fee" name="delivery_fee" value="{{old('delivery_fee')}}">
                                @if($errors->has('delivery_fee'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('delivery_fee') }}</strong>
                                    </span>
                                @endif
                            </div>


                            <div class="form-group">
                                <label for="image">{{__('admin.image')}}</label>
                                <input required type="file" name="image" id="image" data-plugins="dropify"
                                       data-default-file=""/>
                                <p class="text-muted text-center mt-2 mb-0">{{__('admin.upload_image')}}</p>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-success waves-effect waves-light">{{__('admin.create')}}</button>
                                <a type="button" href="{{route('admin.categories.index')}}"
                                   class="btn btn-danger waves-effect waves-light m-l-10">{{__('admin.cancel')}}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
    <script src="{{asset('assets/libs/dropify/dropify.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/form-fileuploads.init.js')}}"></script>
@endsection
