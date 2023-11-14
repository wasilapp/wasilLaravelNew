@extends('admin.layouts.app', ['title' => 'All Notification'])

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
                            <li class="breadcrumb-item active">{{__('admin.all_notification')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.all_notification')}}</h4>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-4">

                                    {{ $notifications->links() }}

                            </div>
                            <div class="col-sm-8">
                                <div class="text-sm-right">
                                    <a type="button" href="{{route('admin.notifications.create')}}"
                                       class="btn btn-primary waves-effect waves-light mb-2 text-white">{{__('admin.send_notification')}}
                                    </a>
                                </div>
                            </div><!-- end col-->
                        </div>

                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap table-hover mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{__('admin.image')}}</th>
                                    <th>{{__('admin.title')}}</th>
                                    <th>{{__('admin.description')}}</th>
                                   
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse ($notifications as $notification)
                                        <tr class="@if($notification->unread()) notification-unread @endif">

                                                <td>
                                                    <a href="{{ $notification->data['url'] }}?notification_id={{ $notification->id }}">

                                                        <div>

                                                            <img src="{{asset( $notification->data['icon'])}}"
                                                                style="object-fit: cover" alt="Image"
                                                                height="40px"
                                                                width="40px">
                                                                
                                                            
                                                        </div>
                                                    </a>
                                                </td>
                                                <td>{{ $notification->data[ app()->getLocale()]['title'] }}</td>
                                                <td>{{ $notification->data[ app()->getLocale()]['body'] }}</td>
                                            
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
