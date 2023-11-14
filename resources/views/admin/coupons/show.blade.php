@extends('admin.layouts.app', ['title' => 'New Coupon'])

@section('css')
@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{env('APP_NAME')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.coupons.index')}}">{{__('admin.coupon')}}</a></li>
                            <li class="breadcrumb-item active">{{__('admin.create')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.create_coupon')}}</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row ">
                            <div class="form-group col-md-6">
                                <label for="image">{{__('admin.added_by')}}</label>
                                @if ($coupon->category_id == 1)
                                <a href="{{ route('admin.shops.show',$coupon->shops->first()->id ) }}">{{ $coupon->shops->first()->name }}</a>

                                @else
                                <a href="{{ route('admin.delivery-boy.show',$coupon->deliveryBoys->first->id ) }}">{{ $coupon->deliveryBoys->first()->name }}</a>

                                @endif
                                
                            </div>

                            <div class="d-flex flex-column justify-content-center col-md-6">
                                <div>{{__("admin.service")}} : <a href="{{route('admin.categories.edit',['id'=>$coupon->category_id])}}">{{$coupon->category->title}}</a></div>
                                <div class="mt-3">{{__("admin.active")}} : 
                                @if ($coupon->is_active)
                                    <span class="text-primary">{{ trans('admin.active') }}</span>
                                @else
                                    <span class="text-danger">{{ trans('admin.inactive') }}</span>
                                @endif</div>
                            </div>
                        </div>
                        
                            <div class="row mt-3">
                                <div class="form-group col-12 col-md-6">
                                    <label for="code">{{__('admin.coupon_code')}}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">#</span>
                                        </div>
                                        <input disabled type="text" class="form-control" value={{ $coupon->code }}
                                            id="code" placeholder="SAVE40" value="{{old('code')}}"
                                            name="code">
                                    </div>
                                </div>
                                
                                <div class="form-group col-12 col-md-6">
                                    <label for="offer">{{__('admin.offer')}} (in %) </label>
                                    <div class="input-group">
                                        <input disabled type="number" step="1" id="offer" value={{ $coupon->offer }} disabled
                                               class="form-control"
                                               name="offer"
                                               placeholder="Offer"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon1">%</span>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="form-group col-12 col-md-6 mb-3">
                                    <label for="min_order">{{__('admin.min_order')}} </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">{{\App\Helpers\AppSetting::$currencySign}}</span>
                                        </div>
                                        <input type="number" value={{ $coupon->min_order }} disabled
                                            class="form-control" name="min_order"
                                            id="min_order" value="{{old('min_order')}}"
                                            placeholder="{{__('admin.min_order')}}">
    
                                    </div>
                                </div>
    
                                <div class="form-group col-12 col-md-6 mb-3">
                                    <label for="max_discount">{{__('admin.max_discount')}} </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">{{\App\Helpers\AppSetting::$currencySign}}</span>
                                        </div>
                                        <input type="number" value={{ $coupon->max_discount }} disabled
                                            class="form-control " name="max_discount"
                                            id="max_discount" value="{{old('max_discount')}}"
                                            placeholder="{{__('admin.max_discount')}}">

                                    </div>
                                </div>
    
                                <div class="form-group col-12 col-md-6">
                                    <label for="expired_at">{{__('admin.expired_at')}}</label>
                                    <input type="date" id="expired_at" disabled
                                        value="{{$coupon->expired_at}}" name="expired_at"
                                        class="form-control">
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.enDescription')}}</label>
                                    <textarea class="form-control" disabled cols="30" rows="10">{{$coupon->getTranslation('description','en')}}</textarea>
                                    
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.arDescription')}}</label>
                                    <textarea class="form-control" disabled cols="30" rows="10">{{$coupon->getTranslation('description','ar')}}</textarea>
                                </div>
                            </div>

                            <div class="custom-control custom-switch">
                                <input disabled type="checkbox" class="custom-control-input" name="for_new_user" id="for_new_user">
                                <label class="custom-control-label" for="for_new_user">{{__('admin.for_only_new_user')}}</label>
                            </div>


                            <div class="custom-control custom-switch mt-2">
                                <input disabled type="checkbox" class="custom-control-input" name="for_only_one_time" id="for_only_one_time" checked>
                                <label class="custom-control-label" for="for_only_one_time">{{__('admin.for_only_one_time')}}</label>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a class="btn btn-primary waves-effect waves-light mr-2" href="{{ route('admin.coupons-requests.accept', ['id' => $coupon->id]) }}">accept</a>
                                <a class="btn btn-danger waves-effect waves-light" href="{{ route('admin.coupons-requests.decline', ['id' => $coupon->id]) }}">decline</a>
                                {{-- <form action="{{ route('admin.coupons-requests.accept', ['id' => $coupon->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary waves-effect waves-light mr-2">{{__('admin.accept')}}</button>
                                </form>

                                <form action="{{ route('admin.coupons-requests.decline', ['id' => $coupon->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{__('admin.decline')}}</button>
                                </form> --}}
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
            $('#expired_at').datetimepicker({
                format: 'MM/DD/YYYY',
                locale: 'en',
                min: (new Date()).toString()
            })
        });
    </script>
    
@endsection
