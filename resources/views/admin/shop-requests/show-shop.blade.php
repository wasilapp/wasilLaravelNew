{{-- {{ dd($shop['manager']); }} --}}
@extends('admin.layouts.app', ['title' => 'Shop'])

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
                            <li class="breadcrumb-item"><a
                                    href="{{route('admin.shops.index')}}">{{__('admin.shop')}}</a></li>
                            <li class="breadcrumb-item active"># {{$shop->id}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{$shop->name}}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <img class="card-img-top" style="object-fit: cover" height="300"
                                    src="{{ asset($shop['manager']->license) }}"
                                    alt="license">
                            </div>
                            <div class="col-12 mt-3">
                                <h4 class="card-title">
                                    {{$shop->name}}
                                </h4>
                                <div class="row">
                                    <div class="col-1">
                                        <i class="far fa-envelope mr-1"></i>
                                    </div>
                                    <div class="col">
                                        <span> {{$shop->email}}</span>
                                    </div>
                                </div>
                                <div class="mt-2 row">
                                    <div class="col-1">
                                        <i class="far fa-map mr-1"></i>
                                    </div>
                                    <div class="col">
                                        <span> {{$shop->address}}</span>
                                    </div>
                                </div>
                                <div class="row text-right">
                                    <div class="col">
                                        <a class="btn btn-outline-primary waves-effect waves-light mr-2"
                                        href="{{route('admin.shops.reviews.show',['id'=> $shop->id])}}">
                                            <i class="mdi mdi-star-outline mr-1"></i> {{__('admin.review')}}
                                        </a>

                                        <a target="_blank" class="  btn btn-outline-primary waves-effect waves-light"
                                        href="{{\App\Models\Shop::generateGoogleMapLocationUrl($shop->latitude,$shop->longitude)}}">
                                            <i class="mdi mdi-map-marker-outline mr-1"></i> {{__('manager.location')}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="row">
                    @if($shop['manager'])
                        <div class="col-12">
                            <div class="card-title">
                                <h5>{{__('admin.managed_by')}}</h5>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="widget-rounded-circle card-box">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="avatar-lg">
                                            <img src="{{ asset($shop['manager']->avatar_url) }}"
                                                class="img-fluid rounded-circle" alt="Image"/>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h5 class="mb-1 mt-2">{{$shop['manager']->name}}</h5>
                                        <p class="mb-2 text-muted">{{$shop['manager']->email}}</p>
                                    </div>
                                </div> <!-- end row-->
                            </div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="card-title">
                                <h5>Assign to</h5>
                            </div>
                        </div>
                        @foreach($available_managers as $manager)
                            <div class="col-12">
                                <div class="widget-rounded-circle card-box">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar-lg">
                                                <img src="{{asset('storage/'.$manager->avatar_url)}}"
                                                    class="img-fluid rounded-circle" alt="Image"/>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h5 class="mb-1 mt-2">{{$manager->name}}</h5>
                                            <p class="mb-2 text-muted">{{$manager->email}}</p>
                                        </div>
                                        <div class="col">
                                            <div class="text-right">
                                                <form
                                                    action="{{route('admin.shops.update',['id'=>$shop->id])}}"
                                                    method="post">
                                                    @csrf
                                                    {{method_field('PATCH')}}
                                                    <input type="hidden" name="manager_id" value="{{$manager->id}}">
                                                    <button type="submit" name="action" value="assign_manager"
                                                            class="btn btn-primary waves-effect waves-light">
                                                        Assign
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="col-12 text-center">
                    <div class="widget-rounded-circle card-box">
                        <div class="row text-center">
                            <form action="{{ route('admin.shop_requests.accept', ['id' => $shop->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary waves-effect waves-light mr-2">{{__('admin.accept')}}</button>
                                <input type="hidden" name="mobile_verified" value="1">
                            </form>

                            <form action="{{ route('admin.shop_requests.decline', ['id' => $shop->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger waves-effect waves-light">{{__('admin.decline')}}</button>
                                <input type="hidden" name="mobile_verified" value="0">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- Plugins js-->
    <script src="{{ $chart->cdn() }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{ $chart->script() }}
@endsection
