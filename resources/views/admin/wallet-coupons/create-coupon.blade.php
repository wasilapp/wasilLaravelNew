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
                    <h4 class="page-title">{{__('admin.create_user_wallet')}}</h4>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.wallet-coupons.store')}}" method="post">
                            @csrf
                           
                             <div class="form-group">
                                <label for="code">{{__('admin.user_number')}}  <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    </div>
                            <select  class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="user_number" >
                                @foreach($users as $user)
                              <option data-tokens="{{ $user->name }}" {{(old('user_number' )== $user->id)?'selected':''}} value="{{$user->id}}">{{ $user->name }}|{{ $user->mobile }}</option>
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
                                           id="price" value="{{old('price')}}"
                                           placeholder="{{__('admin.price')}}">

                                    @if($errors->has('price'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('price') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>


                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="status"   id="for_new_user">
                                <label class="custom-control-label" for="for_new_user">{{__('admin.status')}}</label>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-success waves-effect waves-light">{{__('admin.save')}}</button>
                                <a type="button" href="{{route('admin.coupons.index')}}"
                                   class="btn btn-danger waves-effect waves-light m-l-10">{{__('admin.cancel')}}
                                </a>
                            </div>

 </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')



@endsection
