@extends('admin.layouts.app', ['title' => 'Edit Category'])

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
                            <li class="breadcrumb-item active">{{__('admin.edit')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.editService')}}</h4>
                </div>
            </div>
        </div>


        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.categories.update',['id'=>$category->id])}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            {{method_field('PATCH')}}
                            <input type="hidden" name="id" value="{{$category->id}}">

                            <div class="form-group custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="active"
                                       name="active" {{$category->active ? "checked" : ""}}>
                                <label class="custom-control-label" for="active">{{__('admin.active')}}
                                    ({{__('admin.you_can_disable_or_enable_this_category')}})</label>
                            </div>

                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title">{{__('admin.enTitle')}}</label>
                                    <input type="text" class="form-control @if($errors->has('title.en')) is-invalid @endif" id="titleEn" placeholder="English Title" name="title[en]" value="{{$category->getTranslation('title','en');}}">
                                    @if($errors->has('title.en'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title.en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title">{{__('admin.arTitle')}}</label>
                                    <input type="text" class="form-control @if($errors->has('title.ar')) is-invalid @endif" id="titleAr" placeholder="Arabic Title" name="title[ar]" value="{{$category->getTranslation('title','ar');}}">
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
                                    <textarea class="form-control @if($errors->has('description.en')) is-invalid @endif" id="descriptionEn" name="description[en]" cols="30" rows="10">{{$category->getTranslation('description','en')}}</textarea>
                                    @if($errors->has('description.en'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('description.en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.arDescription')}}</label>
                                    <textarea class="form-control @if($errors->has('description.ar')) is-invalid @endif" id="descriptionAr" name="description[ar]" cols="30" rows="10">{{$category->getTranslation('description','ar');}}</textarea>

                                    @if($errors->has('description.ar'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('description.ar') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group mt-0">
                                <label for="type">{{__('admin.type')}}</label>
                                <select required class="form-control @if($errors->has('title')) is-invalid @endif" id="type" placeholder="Title" name="type" >
                                    <option value="water" @if(($category->type) == 'water') selected @endif >{{__('admin.Water')}}</option>
                                    <option value="gas" @if(($category->type) == 'gas') selected @endif >{{__('admin.Gas')}}</option>
                                </select>
                                @if($errors->has('type'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mt-0">
                                <label for="commesion">{{__('admin.commesion')}}</label>
                                <input type="number" step="0.01" class="form-control @if($errors->has('commesion')) is-invalid @endif" id="commesion" placeholder="commesion" name="commesion" value="{{$category->commesion}}">
                                @if($errors->has('commesion'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('commesion') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group mt-0">
                                <label for="delivery_fee">{{__('admin.delivery_fee')}}</label>
                                <input type="number" step="0.01" class="form-control @if($errors->has('delivery_fee')) is-invalid @endif" id="delivery_fee" placeholder="delivery_fee" name="delivery_fee" value="{{$category->delivery_fee}}">
                                @if($errors->has('delivery_fee'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('delivery_fee') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mt-0">
                                <label for="expedited_fees">{{ trans('message.expedited_fees') }}</label>
                                <input type="number" step="0.01" class="form-control @if($errors->has('expedited_fees')) is-invalid @endif" id="expedited_fees" name="expedited_fees" value="{{$category->expedited_fees}}">
                                @if($errors->has('expedited_fees'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('expedited_fees') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mt-0">
                                <label for="scheduler_fees">{{ trans('message.scheduler_fees') }}</label>
                                <input type="number" step="0.01" class="form-control @if($errors->has('scheduler_fees')) is-invalid @endif" id="scheduler_fees" name="scheduler_fees" value="{{$category->scheduler_fees}}">
                                @if($errors->has('scheduler_fees'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('scheduler_fees') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mt-0">
                                <label for="start_work_time">{{ trans('message.start_work_time') }}</label>
                                <input type="time" step="0.01" class="form-control @if($errors->has('start_work_time')) is-invalid @endif" id="start_work_time" name="start_work_time" value="{{$category->start_work_time}}">
                                @if($errors->has('start_work_time'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('start_work_time') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mt-0">
                                <label for="end_work_time">{{ trans('message.end_work_time') }}</label>
                                <input type="time" step="0.01" class="form-control @if($errors->has('end_work_time')) is-invalid @endif" id="end_work_time" name="end_work_time" value="{{$category->end_work_time}}">
                                @if($errors->has('end_work_time'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('end_work_time') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="image">{{__('admin.image')}}</label>
                                <input type="file" name="image" id="image" data-plugins="dropify"
                                       data-default-file="{{ asset($category->image_url) }}"/>
                                <p class="text-muted text-center mt-2 mb-0">{{__('admin.upload_image')}}</p>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-success waves-effect waves-light">{{__('admin.update')}}</button>
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
