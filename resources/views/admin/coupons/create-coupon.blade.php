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
                        <form action="{{route('admin.coupons.store')}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label for="code">{{__('admin.coupon_code')}}  <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">#</span>
                                        </div>
                                        <input type="text" class="form-control @if($errors->has('code')) is-invalid @endif"
                                               id="code" placeholder="SAVE40" value="{{old('code')}}"
                                               name="code">
                                        @if($errors->has('code'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('code') }}</strong>
                                        </span>
                                        @endif
                                    </div>
    
                                </div>
    
                                
                                <div class="form-group col-12 col-md-6">
                                    <label for="offer">{{__('admin.offer')}} (in %)  <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="1" id="offer" value="{{old('offer')}}"
                                               class="form-control @if($errors->has('offer')) is-invalid @endif"
                                               name="offer"
                                               placeholder="Offer"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="basic-addon1">%</span>
                                        </div>
                                        @if($errors->has('offer'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('offer') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
    
                                <div class="form-group col-12 col-md-6 mb-3">
                                    <label for="min_order">{{__('admin.min_order')}} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">{{\App\Helpers\AppSetting::$currencySign}}</span>
                                        </div>
                                        <input type="number" min="0" max="1000" step="1"
                                               class="form-control @if($errors->has('min_order')) is-invalid @endif" name="min_order"
                                               id="min_order" value="{{old('min_order')}}"
                                               placeholder="{{__('admin.min_order')}}">
    
                                        @if($errors->has('min_order'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('min_order') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
    
                                <div class="form-group col-12 col-md-6 mb-3">
                                    <label for="max_discount">{{__('admin.max_discount')}} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">{{\App\Helpers\AppSetting::$currencySign}}</span>
                                        </div>
                                        <input type="number" min="0" max="1000" step="1"
                                               class="form-control @if($errors->has('max_discount')) is-invalid @endif" name="max_discount"
                                               id="max_discount" value="{{old('max_discount')}}"
                                               placeholder="{{__('admin.max_discount')}}">
    
                                        @if($errors->has('max_discount'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('max_discount') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>
    
                                <div class="form-group col-12 col-md-6">
                                    <label for="expired_at">{{__('admin.expired_at')}}</label>
                                    <input type="date" id="expired_at" min="{{now()->addDays(1)->format('Y-m-d')}}"
                                           value="{{now()->addDays(1)->format('Y-m-d')}}" name="expired_at"
                                           class="form-control @if($errors->has('expired_at')) is-invalid @endif">
                                    @if($errors->has('expired_at'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('expired_at') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.enDescription')}}</label>
                                    <textarea class="form-control @if($errors->has('description.en')) is-invalid @endif" id="descriptionEn" name="description[en]" value="{{old('description[en]')}}" cols="30" rows="10"></textarea>
                                    @if($errors->has('description.en'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('description.en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.arDescription')}}</label>
                                    <textarea class="form-control @if($errors->has('description.ar')) is-invalid @endif" id="descriptionAr" name="description[ar]" value="{{old('description[ar]')}}" cols="30" rows="10"></textarea>

                                    @if($errors->has('description.ar'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('description.ar') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="for_new_user" id="for_new_user">
                                <label class="custom-control-label" for="for_new_user">{{__('admin.for_only_new_user')}}</label>
                            </div>


                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox" class="custom-control-input" name="for_only_one_time" id="for_only_one_time" checked>
                                <label class="custom-control-label" for="for_only_one_time">{{__('admin.for_only_one_time')}}</label>
                            </div>

                            <div class="row my-3">
                                <div class="mt-0 col-12 col-md-6 form-group ">
                                <label for="description">{{__('admin.category')}}  <span class="text-danger">*</span></label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="category_id" id="water" value="1" checked>
                                    <label class="form-check-label" for="water">{{__('admin.water')}}</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="category_id" id="gas" value="2" >
                                    <label class="form-check-label" for="gas">{{__('admin.gas')}}</label>
                                  </div>
                                  @if($errors->has('category_id'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('category_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="mt-0 col-12 col-md-6 form-group ">
                                <label for="description">{{__('admin.type')}}  <span class="text-danger">*</span></label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="general" value="general" checked>
                                    <label class="form-check-label" for="general">{{__('admin.general')}}</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="custom" value="custom" >
                                    <label class="form-check-label" for="custom">{{__('admin.custom')}}</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" id="available" value="available">
                                    <label class="form-check-label" for="available">{{__('admin.available')}}</label>
                                  </div>
                                  @if($errors->has('type'))
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                @endif
                                </div>
                                <div class="mt-0 col-12 col-md-6 form-group" style="" id="shops">
                                    <label class="col-9 control-label pb-2 pt-2">{{trans('admin.shop')}}</label>
                                    <div class="col-sm-12 input-group mb-3">
                                        <div class="input-group">
                                            
                                            <select class="selectpicker shop_id" id="select_shop" data-live-search="true" name="shop_id">
                                                <option value="">select shop</option>
                                                @foreach ($shops as $shop)
                                                    <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-0 col-12 col-md-6 form-group" style="" id="deliveryBoys">
                                    <label class="col-9 control-label pb-2 pt-2">{{trans('admin.deliveryBoy')}}</label>
                                    <div class="col-sm-12 input-group mb-3">
                                        <div class="input-group">
                                            
                                            <select class="selectpicker shop_id" id="select_deliveryBoy" data-live-search="true" name="deliveryBoy_id">
                                                <option value="">select delivery Boy</option>
                                                @foreach ($deliveryBoys as $deliveryBoy)
                                                    <option value="{{ $deliveryBoy->id }}">{{ $deliveryBoy->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        

                            <div class="text-right">
                                <button type="submit" class="btn btn-success waves-effect waves-light">{{__('admin.save')}}</button>
                                <a type="button" href="{{route('admin.coupons.index')}}"
                                   class="btn btn-danger waves-effect waves-light m-l-10">{{__('admin.cancel')}}
                                </a>
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
    <script>
            /*start check if custom */
            $(document).on("change",':radio[name="category_id"]',function() {
                $("#shops").hide();
                $("#deliveryBoys").hide();
                $('input:radio[name=type][value=general]').click();
            });
            $(document).on("change",':radio[name="type"]',function() {
                if(this.value == 'custom'){
                    if($('input[name="category_id"]:checked').val() === '1'){
                        $("#shops").show();
                        $("#deliveryBoys").hide();
                    }else {
                        $("#shops").hide();
                        $("#deliveryBoys").show();
                    } 
                } else {
                    $("#shops").hide();
                    $("#deliveryBoys").hide();
                }
            });

            /*end check if custom */
    </script>
@endsection
