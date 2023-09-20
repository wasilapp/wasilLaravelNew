@extends('admin.layouts.app', ['title' => 'Privacy and Policy'])


@section('css')
    <link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/summernote/summernote.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{env('APP_NAME')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.categories.index')}}">{{__('admin.privacy')}}</a></li>
                            <li class="breadcrumb-item active">{{__('admin.create')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.create_privacy')}}</h4>
                </div>
            </div>
        </div>


        <div class="row justify-content-center">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('user.store.privacy')}}" method="post" enctype="multipart/form-data" >
                            @csrf
                            
                            
                            <div class="col-lg-12">
                            <div class="card-box">     
                           <div class="form-group mt-0">
                            <label for="summernote">{{__('admin.privacy')}}</label>
                            <textarea id="summernote" name="title"
                                      class="@if($errors->has('title')) is-invalid @endif">@if($privacy){!! $privacy->title !!} @endif</textarea>
                            @if($errors->has('title'))
                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>

                        </div>
                     
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

    <script>




        $(document).ready(function () {
            $('#summernote').summernote({
                toolbar: [
                    ['style', ['bold', 'italic']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['view', ['codeview', 'help']],
                ]
            })

    
        });
    </script>

    <script src="{{asset('assets/libs/summernote/summernote.min.js')}}"></script>
       <script src="{{asset('assets/js/pages/form-summernote.init.js')}}"></script>
       
    <script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
    <script src="{{asset('assets/libs/dropify/dropify.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/form-fileuploads.init.js')}}"></script>
@endsection
