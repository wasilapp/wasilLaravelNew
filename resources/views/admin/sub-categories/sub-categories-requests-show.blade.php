@extends('admin.layouts.app', ['title' => 'Show Sub Category'])

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
                            <li class="breadcrumb-item active">{{__('admin.show')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.showSubService')}}</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                            
                            <div class="row ">
                                <div class="form-group col-md-6">
                                    <label for="image">{{__('admin.image')}}</label>
                                    <img src="{{asset($sub_category->image_url)}}" alt="" style="width: 200px">
                                    
                                </div>
    
                                <div class="d-flex flex-column justify-content-center col-md-6">
                                    <div>{{__("admin.service")}} : <a href="{{route('admin.categories.edit',['id'=>$sub_category->category->id])}}">{{$sub_category->category->title}}</a></div>
                                    <div class="mt-3">{{__("admin.active")}} : 
                                    @if ($sub_category->active)
                                        <span class="text-primary">{{ trans('admin.active') }}</span>
                                    @else
                                        <span class="text-danger">{{ trans('admin.inactive') }}</span>
                                    @endif</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title">{{__('admin.enTitle')}}</label>
                                    <input type="text" disabled class="form-control" id="titleEn" name="title[en]" value="{{$sub_category->getTranslation('title','en');}}">
                                    
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title">{{__('admin.arTitle')}}</label>
                                    <input type="text" class="form-control" id="titleAr" name="title[ar]" value="{{$sub_category->getTranslation('title','ar');}}" disabled>
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.enDescription')}}</label>
                                    <textarea disabled class="form-control" id="descriptionEn" name="description[en]" cols="30" rows="10">{{$sub_category->getTranslation('description','en')}}</textarea>
                                    
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.arDescription')}}</label>
                                    <textarea disabled class="form-control @if($errors->has('description.ar')) is-invalid @endif" id="descriptionAr" name="description[ar]" cols="30" rows="10">{{$sub_category->getTranslation('description','ar');}}</textarea>

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="price">{{__('admin.price')}}</label>
                                <input type="text" class="form-control" name="price" value="{{$sub_category->price}}" disabled>
                               
                            </div>
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label for="price">{{__('admin.price')}}</label>
                                    <input disabled type="number" name="price" id="price" class="form-control " value="{{$sub_category->price}}">
                                    
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="quantity">{{__('admin.quantity')}}</label>
                                    <input disabled type="number" name="quantity" id="quantity" class="form-control" value="{{$sub_category->quantity}}">
                                    
                                </div>
                            </div>
                            

                            <div class="d-flex justify-content-end">
                                {{-- <button type="submit" class="btn btn-success waves-effect waves-light">{{__('admin.update')}}</button>
                                <a type="button" href="{{route('admin.sub-categories.index')}}"
                                   class="btn btn-danger waves-effect waves-light m-l-10">{{__('admin.cancel')}}
                                </a> --}}
                                <form action="{{ route('admin.sub-categories-requests.accept', ['id' => $sub_category->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary waves-effect waves-light mr-2">{{__('admin.accept')}}</button>
                                    <input type="hidden" name="mobile_verified" value="1">
                                </form>

                                <form action="{{ route('admin.sub-categories-requests.decline', ['id' => $sub_category->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{__('admin.decline')}}</button>
                                    <input type="hidden" name="mobile_verified" value="0">
                                </form>
                            </div>
                       
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
