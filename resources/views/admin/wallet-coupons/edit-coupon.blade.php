@extends('admin.layouts.app', ['title' => 'Edit Coupon'])

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
                            <li class="breadcrumb-item active">{{__('admin.edit')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.edit_coupon')}}</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.wallet-coupons.update',['id'=>$coupon->id])}}" method="post">
                            @csrf
                            {{method_field('PATCH')}}
                            <div class="form-group">
                                <label for="code">{{__('admin.user_number')}}  <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    </div>
                            <select  class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="user_number" >
                                @foreach($users as $user)
                              <option data-tokens="{{ $user->name }}" {{($coupon->user_id == $user->id)?'selected':''}} value="{{$user->id}}">{{ $user->name }}|{{ $user->mobile }}</option>
                             @endforeach
                            </select>

                                    @if($errors->has('user_number'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('user_number') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </div>
    
                            <div class="form-group mb-3">
                                <label for="price">{{__('admin.price')}} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">{{\App\Helpers\AppSetting::$currencySign}}</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" max="1000" step="1"
                                           class="form-control @if($errors->has('price')) is-invalid @endif" name="price"
                                           id="price" value="{{$coupon->price}}"
                                           placeholder="{{__('admin.price')}}">

                                    @if($errors->has('price'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('price') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>


                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="status" id="is_active" @if($coupon->status) checked value="1" @else value="0" @endif >
                                <label class="custom-control-label" for="is_active">{{__('admin.coupon_activation')}}</label>
                            </div>



                            <div class="text-right">
                                <button type="submit" class="btn btn-success waves-effect waves-light">{{__('admin.update')}}</button>
                                <a type="button" href="{{route('admin.wallet-coupons.index')}}"
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
    <script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
    <script src="{{asset('assets/libs/dropify/dropify.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/form-fileuploads.init.js')}}"></script>
@endsection
