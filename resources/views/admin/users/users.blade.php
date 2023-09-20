@extends('admin.layouts.app', ['title' => 'Products'])

@section('css')
@endsection

@section('content')

    <!-- Start Content-->
    <div class="container-fluid">
        {{-- <x-alert></x-alert> --}}

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{env('APP_NAME')}}</a>
                            </li>
                            <li class="breadcrumb-item active">{{__('admin.users')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.users')}}</h4>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">

                        <div class="btn btn-primary mr-2 ml-2 mb-2">
                            {{ trans('admin.UsersCount') }} : {{ $users_count }}
                        </div>
                        <div class="float-right">
                            {{ $users->links() }}
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{__('admin.image')}}</th>
                                    <th>{{__('admin.name')}}</th>
                                    <th>{{__('admin.email')}}</th>
                                    <th>{{__('admin.mobile')}}</th>
                                    <th>{{__('admin.verified')}}</th>
                                    <th>{{__('admin.status')}}</th>

                                    <th style="width: 82px;">{{__('admin.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            @if($user->avatar_url)
                                                <img src="{{asset($user->avatar_url)}}"
                                                     style="object-fit: cover" alt="OOps"
                                                     height="64px"
                                                     width="64px">
                                            @else
                                                <img src="{{\App\Models\Product::getPlaceholderImage()}}"
                                                     style="object-fit: cover" alt="OOps"
                                                     height="64px"
                                                     width="64px">
                                            @endif
                                        </td>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->email}}</td>
                                        <td>{{$user->mobile}}</td>
                                        <td>
                                            @if($user->mobile_verified)
                                                <span class="text-success">{{__('admin.verified')}}</span>
                                            @else
                                                <span class="text-danger">{{__('admin.not_verified')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->blocked)
                                                <span class="text-danger">{{__('admin.blocked')}}</span>
                                            @else
                                                <span class="text-success">{{__('admin.active')}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{route('admin.users.edit',['id'=>$user->id])}}"
                                                style="font-size: 20px"> <i
                                                    class="mdi mdi-pencil"></i></a>
                                            <form method="POST" action="{{route('admin.users.destroy', [$user->id])}}" class="d-inline" onsubmit="return confirm('Delete this user permanently?')">

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
