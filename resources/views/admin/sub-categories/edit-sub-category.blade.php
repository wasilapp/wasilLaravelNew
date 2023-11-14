@extends('admin.layouts.app', ['title' => 'Edit Sub Category'])

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
                            <li class="breadcrumb-item"><a href="{{route('admin.categories.index')}}">{{__('admin.subServices')}}</a></li>
                            <li class="breadcrumb-item active">{{__('admin.edit')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.editSubService')}}</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.sub-categories.update',['id'=>$sub_category->id])}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            {{method_field('PATCH')}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <input type="hidden" name="category" value="{{ $sub_category->category->id }}">
                            <div class="form-group custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="active"
                                       name="active" {{$sub_category->active ? "checked" : ""}}>
                                <label class="custom-control-label" for="active">{{__('admin.active')}}
                                    ({{__('admin.you_can_disable_or_enable_this_sub_category')}})</label>
                            </div>

                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title">{{__('admin.enTitle')}}</label>
                                    <input type="text" class="form-control @if($errors->has('title.en')) is-invalid @endif" id="titleEn" name="title[en]" value="{{$sub_category->getTranslation('title','en');}}">
                                    @if($errors->has('title.en'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title.en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title">{{__('admin.arTitle')}}</label>
                                    <input type="text" class="form-control @if($errors->has('title.ar')) is-invalid @endif" id="titleAr" name="title[ar]" value="{{$sub_category->getTranslation('title','ar');}}">
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
                                    <textarea class="form-control @if($errors->has('description.en')) is-invalid @endif" id="descriptionEn" name="description[en]" cols="30" rows="10">{{$sub_category->getTranslation('description','en')}}</textarea>
                                    @if($errors->has('description.en'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('description.en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.arDescription')}}</label>
                                    <textarea class="form-control @if($errors->has('description.ar')) is-invalid @endif" id="descriptionAr" name="description[ar]" cols="30" rows="10">{{$sub_category->getTranslation('description','ar');}}</textarea>

                                    @if($errors->has('description.ar'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('description.ar') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="price">{{__('admin.price')}}</label>
                                <textarea name="price" id="price"
                                          class="form-control @if($errors->has('price')) is-invalid @endif">{{$sub_category->price}}
                                </textarea>
                                @if($errors->has('price'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label for="price">{{__('admin.price')}}</label>
                                    <input required type="number" name="price" id="price" class="form-control @if($errors->has('price')) is-invalid @endif" value="{{$sub_category->price}}">
                                    @if($errors->has('price'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('price') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="quantity">{{__('admin.quantity')}}</label>
                                    <input required type="number" name="quantity" id="quantity" class="form-control @if($errors->has('quantity')) is-invalid @endif" value="{{$sub_category->quantity}}">
                                    @if($errors->has('quantity'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('quantity') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                             <div class="form-group">
                                <label for="image">{{__('admin.image')}}</label>
                                <input type="file" name="image" id="image" data-plugins="dropify"
                                       data-default-file="{{asset($sub_category->image_url)}}"/>
                                <p class="text-muted text-center mt-2 mb-0">{{__('admin.upload_image')}}</p>
                            </div>

                            <div>
                                <span>{{__("admin.service")}} : <a href="{{route('admin.categories.edit',['id'=>$sub_category->category->id])}}">{{$sub_category->category->title}}</a></span>
                            </div>


                            <div class="text-right">
                                <button type="submit" class="btn btn-success waves-effect waves-light">{{__('admin.update')}}</button>
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
@endsection
