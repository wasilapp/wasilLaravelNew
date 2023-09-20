@extends('manager.layouts.app', ['title' => 'codes'])

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
                            <li class="breadcrumb-item"><a href="{{route('manager.dashboard')}}">{{env('APP_NAME')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{__('manager.codes')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('manager.codes')}}</h4>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">


                        <div class="float-right">
                            {{ $codes->links() }}
                        </div>
                        <div class="col-sm-12">
                            <div class="text-sm-right">
                                <a type="button" href="{{route('manager.codes.create')}}"
                                   class="btn btn-primary waves-effect waves-light mb-2 text-white">{{__('manager.add_code')}}
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{__('manager.title')}} ID</th>
                                    <th>{{__('manager.max_use')}}</th>
                                    <th>{{__('manager.user')}}</th>

                                    <th style="width: 250px;">{{__('manager.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($codes as $code)
                                    <tr>
                                     <td>{{$code['title']}}</td>
                                     <td>{{$code['max_use']}}</td>
                                     <td><a href="{{route('manager.codes.index',['id'=>$code->user->id])}}">{{$code->user->email}}</a>
                                     </td>

                                     <td>
                                        <a href="{{route('manager.codes.edit',['id'=>$code->id])}}"
                                           style="font-size: 20px"> <i
                                                class="mdi mdi-pencil"></i></a>
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
