@extends('admin.layouts.app', ['title' => 'Banners'])

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
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{env('APP_NAME')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{__('admin.banners')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.banners')}}</h4>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="col-8">
                            <div class="text-right">
                                <a type="button" href="{{route('admin.users-banners.create')}}"
                                class="btn btn-primary waves-effect waves-light text-white">Create banner
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{__('admin.image')}}</th>
                                    <th style="width: 82px;">{{__('admin.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($banners as $banner)
                                    <tr>
                                        <td>
                                            <img src="{{asset($banner->url)}}"
                                                style="object-fit: cover" alt="Image">
                                        </td>
                                        <td>
                                            {{-- <a href="{{route('admin.users.edit',['id'=>$banner->id])}}"
                                                style="font-size: 20px"> <i
                                                    class="mdi mdi-pencil"></i></a> --}}
                                            <form method="POST" action="{{route('admin.users-banners.destroy', [$banner->id])}}" class="d-inline" onsubmit="return confirm('Delete this user permanently?')">

                                                @csrf

                                                <input type="hidden" name="_method" value="DELETE">

                                                <button type="submit" class="btn btn-link p-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="red" d="M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12Z"/></svg>
                                                </button>
                                            </form>
                                        </td>
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
