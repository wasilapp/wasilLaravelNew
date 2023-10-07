@extends('admin.layouts.app', ['title' => 'Delivery boy'])

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
                            <li class="breadcrumb-item active">{{__('admin.delivery_boy')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.delivery_boy')}}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">

                        <div class="float-right mr-2">
                            {{ $delivery_boys->links() }}
                        </div>
                        <div class="col-8">
                        <div class="text-right">
                            <a type="button" href="{{route('admin.delivery-boy.create')}}"
                               class="btn btn-primary waves-effect waves-light text-white">{{__('admin.create_delivery_boy')}}
                            </a>
                        </div>
                    </div>
                        <div class="col-12 mt-3">

                            @if($delivery_boys->count()>0)

                                <div class="table-responsive">
                                    <table class="table table-centered table-nowrap table-hover mb-0">
                                        <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>{{__('admin.image')}}</th>
                                            <th>{{__('admin.name')}}</th>
                                            <th>{{__('admin.online')}}</th>
                                            <th>{{__('admin.rating')}}</th>
                                            <th>{{__('admin.orders')}}</th>
                                              <th>{{__('admin.shop')}}</th>
                                            <th>{{__('admin.revenue')}}</th>
                                            <th>{{__('admin.status')}}</th>
                                            <th style="width: 82px;">{{__('admin.action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($delivery_boys as $delivery_boy)
                                            <tr>
                                                <td>{{$delivery_boy->id}}
                                                </td>
                                                <td>

                                                    <img
                                                        src="{{ asset($delivery_boy->avatar_url) }}"
                                                        alt="image" class="img-fluid avatar-sm rounded-circle">
                                                </td>
                                                <td>{{$delivery_boy->name}}</td>
                                                <td>
                                                    @if($delivery_boy->is_offline)
                                                        <span class="bg-danger mr-1"
                                                              style="border-radius: 50%;width: 8px;height: 8px;  display: inline-block;"></span> {{__('admin.offline')}}
                                                    @else
                                                        <span class="bg-primary mr-1"
                                                              style="border-radius: 50%;width: 8px;height: 8px;  display: inline-block;"></span> {{__('admin.online')}}
                                                    @endif

                                                </td>
                                                <td>
                                                    @for($i=0;$i<5;$i++)
                                                        <i class="mdi @if($i<$delivery_boy['rating']) mdi-star @else mdi-star-outline @endif"
                                                           style="font-size: 18px; margin-left: -4px; color: @if($i<$delivery_boy['rating'])  {{\App\Helpers\ColorUtil::getColorFromRating($delivery_boy['rating'])}} @else black @endif"></i>
                                                    @endfor
                                                    <p class="d-inline">({{$delivery_boy['total_rating']}})</p>
                                                </td>
                                                <td>{{$delivery_boy->orders_count}}</td>
                                                 <td>{{ ($delivery_boy->shop ? $delivery_boy->shop->name : 'FreeLance' )}}</td>
                                                <td>{{\App\Helpers\AppSetting::$currencySign}} {{\App\Helpers\CurrencyUtil::doubleToString($delivery_boy->revenue)}}</td>
                                                <td>
                                                    @if ($delivery_boy->is_approval == 0)
                                                        <p class="text-warning">{{__('admin.pending')}}</p>
                                                    @elseif($delivery_boy->is_approval == 1)
                                                        <p class="text-primary">{{__('admin.Accepted by the manager')}}</p>
                                                    @elseif($delivery_boy->is_approval == -1)
                                                        <p class="text-danger"> {{__('admin.Rejected by the manager')}}</p>
                                                    @elseif($delivery_boy->is_approval == -2)
                                                        <p class="text-danger"> {{__('admin.Rejected by admin')}}</p>
                                                    @else
                                                        <p class="text-success"> {{__('admin.Accepted by admin')}}</p>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{route('admin.delivery-boy.reviews.show',['id'=>$delivery_boy->id])}}"
                                                       style="font-size: 20px"> <i
                                                            class="mdi mdi-star "></i>
                                                    </a>
                                                    <a href="{{route('admin.delivery-boy.show',['id'=>$delivery_boy->id])}}"
                                                       style="font-size: 20px"> <i
                                                            class="mdi mdi-eye "></i></a>
                                                    <form method="POST" action="{{route('admin.delivery-boy.destroy', [$delivery_boy->id])}}" class="d-inline" onsubmit="return confirm('Delete this user permanently?')">
                                                        @csrf
                                                        <input type="hidden" name="_method" value="DELETE">

                                                        <button type="submit" class="btn btn-link p-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="red" d="M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12Z"/></svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>

                            @else
                                <h3>{{__('admin.there_is_no_deliveryBoy_yet')}}</h3>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('script')

@endsection
