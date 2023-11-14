@extends('admin.layouts.app', ['title' => 'Sub Categories'])

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
                            <li class="breadcrumb-item active">{{__('admin.wallets')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.wallets')}}</h4>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-4">
                                    {{ $wallets->links() }}
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{__('admin.title')}}</th>
                                    <th>{{__('admin.description')}}</th>
                                    <th>{{__('admin.service')}}</th>
                                    <th>{{__('admin.shop')}}</th>
                                    <th>{{__('admin.price')}}</th>
                                    <th style="width: 82px;">{{__('admin.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($wallets as $wallet)

                                    <tr>

                                        <td>{{$wallet->title}}</td>
                                        <td>{{$wallet->description}}</td>
                                        <td><a href="{{route('admin.sub-categories.edit',['id'=>$wallet->subcategory_id])}}">{{$wallet->subCategory->title}}</a>
                                        </td>
                                        <td> {{ $wallet->shop->name}} </td>
                                        <td> {{$wallet->price}} </td>
                                        <td class="d-flex gap-2">
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
