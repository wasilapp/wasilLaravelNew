@extends('admin.layouts.app', ['title' => 'send notification'])

@section('css')
<style>
    #users {
        display: none
    }

</style>

@endsection

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{env('APP_NAME')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.notifications.index')}}">{{__('admin.notification')}}</a></li>
                            <li class="breadcrumb-item active">{{__('admin.create')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.send_notification')}}</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.notifications.send')}}" method="post">
                            @csrf
                            
                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title">{{__('admin.enTitle')}}</label>
                                    <input type="text" class="form-control @if($errors->has('title.en')) is-invalid @endif" id="titleEn" name="title[en]" value="{{old('title[en]')}}">
                                    @if($errors->has('title.en'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title.en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title">{{__('admin.arTitle')}}</label>
                                    <input type="text" class="form-control @if($errors->has('title.ar')) is-invalid @endif" id="titleAr" name="title[ar]" value="{{old('title[ar]')}}">
                                    @if($errors->has('title.ar'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title.ar') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.enDescription')}}</label>
                                    <textarea class="form-control @if($errors->has('body.en')) is-invalid @endif" id="descriptionEn" name="body[en]" value="{{old('body[en]')}}" cols="30" rows="10"></textarea>
                                    @if($errors->has('body.en'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('body.en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="form-group mt-0 col-md-6">
                                    <label for="description">{{__('admin.arDescription')}}</label>
                                    <textarea class="form-control @if($errors->has('body.ar')) is-invalid @endif" id="descriptionAr" name="body[ar]" value="{{old('body[ar]')}}" cols="30" rows="10"></textarea>

                                    @if($errors->has('body.ar'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('body.ar') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group mt-0 col-md-6">
                                    <label for="title">{{__('admin.url')}}</label>
                                    <input type="text" class="form-control @if($errors->has('url')) is-invalid @endif" id="url" name="url" value="{{old('url')}}">
                                    @if($errors->has('url'))
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('url') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <h5 class="my-2">{{__('admin.send-to')}}</h5>
                            <div class="row">
                                <div class="mt-0 col-12 col-md-6 form-group d-flex flex-column">
                                    <div class="form-check form-check-inline my-2">
                                    <input class="form-check-input" type="radio" name="type" id="all" value="all" checked>
                                    <label class="form-check-label" for="all">{{__('admin.allApplicationsUsers')}}</label>
                                  </div>
                                  <div class="form-check form-check-inline my-2">
                                    <input class="form-check-input" type="radio" name="type" id="allManagers" value="allManagers" >
                                    <label class="form-check-label" for="allManagers">{{__('admin.allManagers')}}</label>
                                  </div>
                                  <div class="form-check form-check-inline my-2">
                                    <input class="form-check-input" type="radio" name="type" id="allDeliveryBoys" value="allDeliveryBoys">
                                    <label class="form-check-label" for="allDeliveryBoys">{{__('admin.allDeliveryBoys')}}</label>
                                  </div>
                                  <div class="form-check form-check-inline my-2">
                                    <input class="form-check-input" type="radio" name="type" id="allUsers" value="allUsers">
                                    <label class="form-check-label" for="allUsers">{{__('admin.allUsers')}}</label>
                                  </div>
                                  <div class="form-check form-check-inline my-2">
                                    <input class="form-check-input" type="radio" name="type" id="specific-manager" value="specific-manager">
                                    <label class="form-check-label" for="specific-manager">{{__('admin.specific-manager')}}</label>
                                  </div>
                                  <div class="form-check form-check-inline my-2">
                                    <input class="form-check-input" type="radio" name="type" id="specific-delivery-boy" value="specific-delivery-boy">
                                    <label class="form-check-label" for="specific-delivery-boy">{{__('admin.specific-delivery-boy')}}</label>
                                  </div>
                                  <div class="form-check form-check-inline my-2">
                                    <input class="form-check-input" type="radio" name="type" id="specific-user" value="specific-user" >
                                    <label class="form-check-label" for="specific-user">{{__('admin.specific-user')}}</label>
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
                                            
                                            <select class="selectpicker shop_id" id="select_manager" data-live-search="true" name="manager_id">
                                                <option value="">select manager</option>
                                                @foreach ($managers as $manager)
                                                    <option value="{{ $manager->id }}">{{ $manager->name }} / {{ $manager->shop->name }} / {{ $manager->mobile }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-0 col-12 col-md-6 form-group" style="" id="deliveryBoys">
                                    <label class="col-9 control-label pb-2 pt-2">{{trans('admin.drivers')}}</label>
                                    <div class="col-sm-12 input-group mb-3">
                                        <div class="input-group">
                                            
                                            <select class="selectpicker shop_id" id="select_deliveryBoy" data-live-search="true" name="deliveryBoy_id">
                                                <option value="">select driver</option>
                                                @foreach ($deliveryBoys as $deliveryBoy)
                                                    <option value="{{ $deliveryBoy->id }}">{{ $deliveryBoy->name }} / {{ $deliveryBoy->mobile }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-0 col-12 col-md-6 form-group" style="" id="users">
                                    <label class="col-9 control-label pb-2 pt-2">{{trans('admin.users')}}</label>
                                    <div class="col-sm-12 input-group mb-3">
                                        <div class="input-group">
                                            
                                            <select class="selectpicker shop_id" id="select_user" data-live-search="true" name="user_id">
                                                <option value="">select user</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} / {{ $user->mobile }}</option>
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
            $(document).on("change",':radio[name="type"]',function() {
                $("#deliveryBoys").hide();
                $("#shops").hide();
                $("#users").hide();
                if(this.value == 'specific-manager'){
                    $("#deliveryBoys").hide();
                    $("#shops").show();
                    $("#users").hide();
                }else if(this.value == 'specific-delivery-boy'){
                    $("#deliveryBoys").show();
                    $("#shops").hide();
                    $("#users").hide();
                }else if(this.value == 'specific-user'){
                    $("#deliveryBoys").hide();
                    $("#shops").hide();
                    $("#users").show();
                } else {
                    $("#deliveryBoys").hide();
                    $("#shops").hide();
                    $("#users").hide();
                }
            
            });

            /*end check if custom */
    </script>
@endsection
