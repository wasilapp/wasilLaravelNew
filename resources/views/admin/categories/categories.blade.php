@extends('admin.layouts.app', ['title' => 'Categories'])

@section('css')
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
                            <li class="breadcrumb-item active">{{__('admin.services')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.services')}}</h4>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-4">

                                    {{ $categories->links() }}

                            </div>
                            <div class="col-sm-8">
                                <div class="text-sm-right">
                                    <a type="button" href="{{route('admin.categories.create')}}"
                                       class="btn btn-primary waves-effect waves-light mb-2 text-white">{{__('admin.addService')}}
                                    </a>
                                </div>
                            </div><!-- end col-->
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{__('admin.image')}}</th>
                                    <th>{{__('admin.service')}}</th>
                                    <th>{{__('admin.commesion')}}</th>

                                    <th style="width: 82px;">{{__('admin.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $category)

                                    <tr>
                                        <td>
                                            <div>
                                                <img src="{{ asset( $category->image_url) }}" style="object-fit: cover" alt="OOps"
                                                     height="40px"
                                                     width="40px">
                                            </div>
                                        </td>
                                        <td>{{$category->title}}</td>
                                        <td>{{$category->commesion}}</td>
                                        <td>
                                            <a href="{{route('admin.categories.edit',['id'=>$category->id])}}" style="font-size: 20px"> <i
                                                    class="mdi mdi-pencil"></i></a>

                                            <a onclick="return confirm('Deleting this category will require deleting the related subCategories. Are you sure to delete it?')"
                                                href="{{route('admin.categories.delete',['id'=>$category->id])}}" style="font-size: 20px"> <i
                                                    class="mdi mdi-trash-can"></i></a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div>
        </div>
    </div> <!-- container -->

@endsection

@section('script')
@endsection
