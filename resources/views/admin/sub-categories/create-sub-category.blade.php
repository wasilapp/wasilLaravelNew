@extends('admin.layouts.app', ['title' => 'New Sub Category'])

@section('css')
 <link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <div class="container-fluid">
        <x-alert></x-alert>

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{env('APP_NAME')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.sub-categories.index')}}">{{__('admin.subServices')}}</a></li>
                            <li class="breadcrumb-item active">{{__('admin.create')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.addSubService')}}</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.sub-categories.store')}}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title[en]">{{__('admin.enTitle')}}</label>
                                    <input required type="text" class="form-control @if($errors->has('title.en')) is-invalid @endif" id="title[en]" name="title[en]" value="{{old('title[en]')}}">
                                    @if($errors->has('title.en'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title.en') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group mt-0 col-md-6">
                                    <label for="title[ar]">{{__('admin.arTitle')}}</label>
                                    <input required type="text" class="form-control @if($errors->has('title.ar')) is-invalid @endif" id="title[ar]" name="title[ar]" value="{{old('title[ar]')}}">
                                    @if($errors->has('title.ar'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title.ar') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.enDescription')}}</label>
                                    <textarea class="form-control @if($errors->has('description.en')) is-invalid @endif" id="descriptionEn" name="description[en]" value="{{old('description[en]')}}" cols="30" rows="10"></textarea>
                                    @if($errors->has('description.en'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('description.en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.arDescription')}}</label>
                                    <textarea class="form-control @if($errors->has('description.ar')) is-invalid @endif" id="descriptionAr" name="description[ar]" value="{{old('description[ar]')}}" cols="30" rows="10"></textarea>

                                    @if($errors->has('description.ar'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('description.ar') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label for="price">{{__('admin.price')}}</label>
                                    <input required type="number" name="price" id="price" class="form-control @if($errors->has('price')) is-invalid @endif">
                                    @if($errors->has('price'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('price') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="quantity">{{__('admin.quantity')}}</label>
                                    <input required type="number" name="quantity" id="quantity" class="form-control @if($errors->has('quantity')) is-invalid @endif">
                                    @if($errors->has('quantity'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('quantity') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group mt-0 col-12 col-md-6  pb-2 pt-2">
                                    <label for="is_primary  pb-2 pt-2">{{__('admin.is_primary')}}</label>
                                    <input type="checkbox" class="@if($errors->has('is_primary')) is-invalid @endif" id="is_primary" name="is_primary" value="1">
                                    @if($errors->has('is_primary'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('is_primary') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-0 col-12 col-md-6 form-group " id="shops">
                                    <label class="col-9 control-label pb-2 pt-2">{{trans('admin.shop')}}</label>
                                    <div class="col-sm-12 input-group mb-3">
                                        <div class="input-group">
                                            
                                            <select class="selectpicker shop_id" id="select_shop" data-live-search="true" name="shop_id">
                                                <option value="">select shop</option>
                                                @foreach ($shops as $shop)
                                                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="category">{{__('manager.category')}} <span class="text-danger">*</span></label>
                                <select required class="form-control" name="category" id="category">
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">{{$category->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="image">{{__('admin.image')}}</label>
                                <input required type="file" name="image" id="image" data-plugins="dropify"
                                       data-default-file=""/>
                                <p class="text-muted text-center mt-2 mb-0">{{__('admin.upload_image')}}</p>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-success waves-effect waves-light">{{__('admin.create')}}</button>
                                <a type="button" href="{{route('admin.sub-categories.index')}}"
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
    <script>
            /*start check if is_primary */
            $("#is_primary").change(function(){
                $("#shops").toggleClass('d-none');
            });
            /*end check if is_primary */
    </script>
@endsection
