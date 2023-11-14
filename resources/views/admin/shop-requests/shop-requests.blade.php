@extends('admin.layouts.app', ['title' => 'Shop Request'])

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
                            <li class="breadcrumb-item active">{{__('admin.shop_revenue')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.shop_request')}}</h4>
                </div>
            </div>
        </div>


        @if($have_shop_request)
            <div class="row mt-3">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>{{__('admin.image')}}</th>
                                <th>{{__('admin.name')}}</th>
                                <th>{{__('admin.manager_name')}}</th>
                                {{-- <th>{{__('admin.barcode')}}</th> --}}
                                <th style="width: 82px;">{{__('admin.action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($shops as $index => $shop)
                                    <tr>
                                        <td>{{$index +1}}
                                        </td>
                                        <td>
                                            <img
                                                src="{{ asset($shop->manager->avatar_url) }}"
                                                alt="image" class="img-fluid rounded" width="100">
                                        </td>
                                        <td>{{$shop->name}}</td>
                                        <td>
                                            {{$shop['manager']->name}}
                                        </td>
                                        {{-- <td>{{$shop->barcode}}</td> --}}
                                        <td class="d-flex gap-2">
                                            <a href="{{route('admin.shop_requests.show',['id'=>$shop->id])}}"
                                                style="font-size: 20px"> <i
                                                     class="mdi mdi-eye"></i></a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h3>{{__('admin.there_is_no_shop_request')}}</h3>
                                </div>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>
            </div>
        @endif
    </div>

@endsection

@section('script')

@endsection
