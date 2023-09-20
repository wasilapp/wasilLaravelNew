@extends('admin.layouts.app', ['title' => 'Edit Sub Category'])

@section('css')

@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{env('APP_NAME')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('manager.codes.index')}}">{{__('admin.codes')}}</a></li>
                            <li class="breadcrumb-item active">{{__('admin.edit')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.codes')}}</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('manager.codes.update',['id'=>$code->id])}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            {{method_field('PATCH')}}



                            <div class="form-group mt-0">
                                <label for="title">{{__('admin.code')}}</label>
                                <input type="text" class="form-control"
                                       id="title" placeholder="Title" name="title" value="{{$code->title}}">
                            </div>

                            <div class="form-group">
                                <label for="max_use">{{__('admin.max_use')}}</label>
                                <textarea name="max_use" id="max_use"
                                          class="form-control @if($errors->has('max_use')) is-invalid @endif"
                                          placeholder="max_use">{{$code->max_use}}
                                </textarea>
                                @if($errors->has('max_use'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('max_use') }}</strong>
                                    </span>
                                @endif
                            </div>

                            {{-- <div>
                                <span>{{__("admin.user")}} : <a href="{{route('manager.codes.index',['id'=>$code->user->id])}}">{{$code->user->email}}</a></span>
                            </div> --}}


                            <div class="text-right">
                                <button type="submit" class="btn btn-success waves-effect waves-light">{{__('admin.update')}}</button>
                                <a type="button" href="{{route('manager.codes.index')}}"
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
@endsection
