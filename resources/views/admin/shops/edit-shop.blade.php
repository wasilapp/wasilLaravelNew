@extends('admin.layouts.app', ['title' => 'Edit Shop'])

@section('css')
    <link href="{{asset('assets/libs/summernote/summernote.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/libs/dropify/dropify.min.css')}}" rel="stylesheet" type="text/css"/>
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
                                    href="{{route('admin.shops.index')}}">{{__('admin.shops')}}</a></li>
                            <li class="breadcrumb-item active">{{__('admin.edit')}}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{__('admin.edit_shop')}}</h4>
                </div>
            </div>
        </div>


        <div class="row justify-content-center">
            <div class="col-12">
                <form action="{{route('admin.shops.update',['id'=>$shop->id])}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    {{method_field('PATCH')}}
                    <input type="hidden" name="id" value="{{ $shop->id }}">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <h4 class="card-title">{{__('admin.general')}}</h4>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="row">
                                        <div class="row col-12 col-md-12 mb-4">
                                            <div class="col-12 col-md-6">
                                                <label for="image">{{__('admin.image')}}</label>
                                                <input type="file" name="admin[avatar_url]" id="image" data-plugins="dropify"
                                                    data-default-file="{{ asset($shop->manager['avatar_url']) }}"
                                                    class="form-control"/>
                                                <p class="text-muted text-center mt-2 mb-0">{{__('admin.upload_image')}}</p>
                                                @if($errors->has('admin.avatar_url'))
                                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('admin.avatar_url') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="license">{{__('admin.license')}}</label>
                                                <input type="file" name="admin[license]" id="license" data-plugins="dropify"
                                                    data-default-file="{{ asset($shop->manager['license']) }}"
                                                    class="form-control"/>
                                                <p class="text-muted text-center mt-2 mb-0">{{__('admin.upload_license')}}</p>
                                                @if($errors->has('admin.license'))
                                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('admin.license') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-12">
                                            <div class="row">
                                                <div class="form-group mt-0 col-md-6">
                                                    <label for="name">{{ trans('admin.shop_name_english') }}</label>
                                                    <input type="text" class="form-control @if($errors->has('shop.name.en')) is-invalid @endif" id="nameEn" name="shop[name][en]" value="{{$shop->getTranslation('name','en');}}">
                                                    @if($errors->has('shop.name.en'))
                                                        <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('shop.name.en') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="form-group mt-0 col-md-6">
                                                    <label for="name">{{ trans('admin.shop_name_arabic') }}</label>
                                                    <input type="text" class="form-control @if($errors->has('shop.name.ar')) is-invalid @endif" id="nameAr" name="shop[name][ar]" value="{{$shop->getTranslation('name','ar');}}">
                                                    @if($errors->has('shop.name.ar'))
                                                        <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('shop.name.ar') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12 mb-3">
                                                    <label for="mobile">{{__('admin.mobile')}}</label>
                                                    <input type="tel"
                                                            class="form-control @if($errors->has('admin.mobile')) is-invalid @endif"
                                                            id="mobile" name="admin[mobile]"
                                                            value="{{$shop->manager['mobile']}}">
                                                    @if($errors->has('admin.mobile'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('admin.mobile') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group mt-0 col-md-6">
                                                    <label for="name">{{ trans('admin.manager_name_english') }}</label>
                                                    <input type="text" class="form-control @if($errors->has('admin.name.en')) is-invalid @endif" id="nameEn" name="admin[name][en]" value="{{$shop->manager->getTranslation('name','en');}}">
                                                    @if($errors->has('admin.name.en'))
                                                        <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('admin.name.en') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="form-group mt-0 col-md-6">
                                                    <label for="name">{{ trans('admin.manager_name_arabic') }}</label>
                                                    <input type="text" class="form-control @if($errors->has('admin.name.ar')) is-invalid @endif" id="nameAr" name="admin[name][ar]" value="{{$shop->manager->getTranslation('name','ar');}}">
                                                    @if($errors->has('admin.name.ar'))
                                                        <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('admin.name.ar') }}</strong>
                                                        </span>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row col-12 col-md-12">
                                            <div class="form-group col-md-6 mb-3">
                                                <label for="email">{{__('admin.email')}}</label>
                                                <input type="text"
                                                        class="form-control @if($errors->has('admin.email')) is-invalid @endif"
                                                        id="email" name="admin[email]"
                                                        value="{{$shop->manager['email']}}">
                                                @if($errors->has('admin.email'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('admin.email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6 mb-3">
                                                <label for="password">{{ trans('admin.password') }}</label>
                                                <input type="password"
                                                        class="form-control @if($errors->has('admin.password')) is-invalid @endif"
                                                        id="password" name="admin[password]" value="" autocomplete="new-password">
                                                @if($errors->has('admin.password'))
                                                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('admin.password') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <h4 class="card-title">{{__('admin.location')}}</h4>
                                </div>

                                <div class="col-12">
                                    <div class="form-group mt-0">
                                        <label for="address">{{__('admin.address')}}</label>
                                        <input type="text"
                                               class="form-control @if($errors->has('address')) is-invalid @endif"
                                               id="address" name="address"
                                               value="{{$shop->address}}">
                                        @if($errors->has('address'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('address') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group mt-0">
                                        <label for="latitude">{{__('admin.latitude')}}</label>
                                        <input type="text"
                                               class="form-control @if($errors->has('latitude')) is-invalid @endif"
                                               id="latitude" name="latitude"
                                               value="{{$shop->latitude}}">
                                        @if($errors->has('latitude'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('latitude') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group mt-0">
                                        <label for="longitude">{{__('admin.longitude')}}</label>
                                        <input type="text"
                                               class="form-control @if($errors->has('longitude')) is-invalid @endif"
                                               id="longitude" name="longitude"
                                               value="{{$shop->longitude}}">
                                        @if($errors->has('longitude'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('longitude') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <h4 class="col-12">{{ trans('admin.subServices') }}</h4>
                                            <div class="tab__content w-100">
                                                <div class="table-container text-center" >
                                                    <table class="table table-striped table-hover"  id="option-table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">{{ trans('admin.subService') }} </th>
                                                                <th scope="col">{{ trans('manager.price') }} </th>
                                                                <th scope="col">{{ trans('manager.quantity') }} </th>
                                                                <th scope="col">{{ trans('manager.operations') }} </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="option">
                                                            @foreach ($shopSubcategories as $subcategory)
                                                                <tr data-id="{{ $subcategory->id }}">
                                                                    <input type="hidden" class="form-control" name="subcategories[{{ $subcategory->id }}][sub_category_id]" autofocus value="{{  $subcategory->id }}">
                                                                    <td > {{  $subcategory->title }} </td>
                                                                    <td ><input type="text" class="form-control" name="subcategories[{{ $subcategory->id }}][price]" autofocus value="{{  $subcategory->pivot->price }}"></td>
                                                                    <td><input type="text" class="form-control" name="subcategories[{{ $subcategory->id }}][quantity]" autofocus value="{{ $subcategory->pivot->quantity }}"></td>
                                                                    <td><span class="btn btn-danger delete">{{ trans('manager.delete') }}</span> </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                               
                                                <div class="add-option mt-5">
                                                    <div class="col-md-12">
                                                        <h6>{{ trans('manager.add-new-sub_category') }}</h6>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 col-md-4 form-group">
                                                            <label class="col-9 control-label pb-2 pt-2">{{trans('admin.subService')}}</label>
                                                            <div class="col-sm-12 input-group mb-3">
                                                                <div class="input-group">
                                                                    <select class="selectpicker sub_category_id" data-live-search="true">
                                                                        @foreach ($subcategories as $sub)
                                                                            <option value="{{ $sub->id }}">{{ $sub->title }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-4 form-group">
                                                            <label class="col-9 control-label pb-2 pt-2">{{trans('manager.price')}}</label>
                                                            <div class="col-sm-12 input-group mb-3">
                                                                <div class="input-group">
                                                                    <input id="arTitle" type="text" class="form-control price"  autofocus>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-4 form-group">
                                                            <label class="col-9 control-label pb-2 pt-2">{{trans('manager.quantity')}}</label>
                                                            <div class="col-sm-12 input-group mb-3">
                                                                <div class="input-group">
                                                                    <input id="enTitle" type="text" class="form-control quantity"  autofocus>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-center m-auto">
                                                            <button type="button" class="btn btn-primary addOPtion">{{trans('manager.add')}}</button>
                                                        </div>
                                                    </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <h4 class="card-title">{{__('manager.delivery_boy')}}</h4>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="custom-checkbox custom-control">
                                        <input class="custom-control-input" type="checkbox" id="available_for_delivery"
                                            name="available_for_delivery"
                                            @if($shop->available_for_delivery) checked @endif>
                                        <label class="custom-control-label"
                                            for="available_for_delivery">{{__('manager.available_for_delivery')}}</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group mt-0">
                                        <label for="delivery_range">{{__('manager.delivery_range')}}</label>
                                        <div class="input-group">
                                            <input type="number" step="1" min="0"
                                                   class="form-control @if($errors->has('delivery_range')) is-invalid @endif"
                                                   id="delivery_range" name="delivery_range"
                                                   value="{{$shop->delivery_range}}">
                                            @if($errors->has('delivery_range'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('delivery_range') }}</strong>
                                                </span>
                                            @endif
                                            <div class="input-group-append">
                                                <span class="input-group-text"
                                                    id="basic-addon1">{{ trans('admin.Meter') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group mt-0">
                                        <label for="minimum_delivery_charge">{{__('manager.minimum_delivery_charge')}}</label>
                                        <div class="input-group cost-shop">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                    id="basic-addon1">{{\App\Helpers\AppSetting::$currencySign}}</span>
                                            </div>
                                            <input type="number" step="1" min="0"
                                                class="form-control @if($errors->has('minimum_delivery_charge')) is-invalid @endif"
                                                id="minimum_delivery_charge" placeholder="{{__('manager.minimum_delivery_charge')}}" name="minimum_delivery_charge"
                                                value="{{$shop->minimum_delivery_charge}}">
                                            @if($errors->has('minimum_delivery_charge'))
                                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('minimum_delivery_charge') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group mt-0">
                                        <label for="delivery_cost_multiplier">{{__('manager.delivery_cost_multiplier')}}</label>
                                        <div class="input-group cost-shop">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                    id="basic-addon1">{{\App\Helpers\AppSetting::$currencySign}}</span>
                                            </div>
                                            <input type="number" step="1" min="0"
                                                class="form-control @if($errors->has('delivery_cost_multiplier')) is-invalid @endif"
                                                id="delivery_cost_multiplier" placeholder="{{__('manager.delivery_cost_multiplier')}}" name="delivery_cost_multiplier"
                                                value="{{$shop->delivery_cost_multiplier}}">
                                            @if($errors->has('delivery_cost_multiplier'))
                                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('delivery_cost_multiplier') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <h4 class="card-title">{{__('manager.other')}}</h4>
                                </div>
                                <div class="col-12 col-md-12 mb-3">
                                    <div class="custom-checkbox custom-control">
                                        <input class="custom-control-input" type="checkbox" name="open" id="open"
                                            @if($shop->open) checked @endif>
                                        <label class="custom-control-label" for="open">{{__('manager.open')}}</label>
                                    </div>
                                </div>

                                {{-- <div class="col-12 col-md-6">
                                    <div class="form-group mt-0">
                                        <label for="default_tax">{{__('manager.default_tax')}}</label>
                                        <div class="input-group">
                                            <input type="number" step="1" min="0"
                                                   class="form-control @if($errors->has('default_tax')) is-invalid @endif"
                                                   id="default_tax" placeholder="Default Tax" name="default_tax"
                                                   value="{{$shop->default_tax}}">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="basic-addon1">%</span>
                                            </div>
                                            @if($errors->has('default_tax'))
                                                <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('default_tax') }}</strong>
                                        </span>
                                            @endif
                                        </div>
                                    </div>

                                </div> --}}

                                {{-- <div class="col-12 col-md-6">
                                    <div class="form-group mt-0">
                                        <label for="admin_commission">{{__('manager.admin_commission')}}</label>
                                        <div class="input-group">
                                            <input type="number" step="1"
                                                   class="form-control"
                                                   id="admin_commission" placeholder="{{__('manager.admin_commission')}}" name="admin_commission"
                                                   value="{{$shop->admin_commission}}">
                                            <div class="input-group-append">
                                                <span class="input-group-text"
                                                      id="basic-addon1">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col mt-3 mb-3">
                        <div class="text-right">
                            <a href="{{route('admin.shops.index')}}" type="button"
                                class="btn w-sm btn-light waves-effect">{{__('admin.cancel')}}</a>
                            <button type="submit"
                                    class="btn btn-success waves-effect waves-light">{{__('admin.update')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $('#summernote').summernote({
                toolbar: [
                    ['style', ['bold', 'italic']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['view', ['codeview', 'help']],
                ]
            }).code("{{$shop->description}}")
        });
        
    </script>
<script>
    
    $(document).ready(function() {

        
        /* start add option */
        $(document).on('click', '.addOPtion', function() {
                    let id = $('.sub_category_id').find(":selected").val();
                    let title = $('.sub_category_id').find(":selected").html();
                    let price = $('.price').val();
                    let quantity = $('.quantity').val();

                    if ($(`table tr[data-id="${id}"]`).length > 0) {
                        swal(
                            '{{trans("admin.this service already selected")}}', {
                                icon: "warning",
                                button: '{{trans("admin.ok")}}',
                        });
                    } else  if(price ==''  ||  quantity =='' ){
                        swal(
                            '{{trans("admin.pricequantity")}}', {
                                icon: "warning",
                                button: '{{trans("admin.ok")}}',
                        });

                    }else{
                        $('.option').append('<tr data-id='+id+'>\
                                <input type="hidden" class="form-control" name="subcategories['+id+'][sub_category_id]" autofocus value="'+id+'">\
                                <td >'+title+'</td>\
                                <td ><input type="text" class="form-control" name="subcategories['+id+'][price]" autofocus value="'+price+'"></td>\
                                <td><input type="text" class="form-control" name="subcategories['+id+'][quantity]" autofocus value="'+quantity+'"></td>\
                                <td><span class="btn btn-danger delete">{{ trans('manager.delete') }}</span> </td>\
                            </tr>');
                        $('.price').val('');
                        $('.quantity').val('');
                        $('.sub_category_id').find(":selected").val()
                        
                    }
                });
        /* end add option */
        /* start delete option */
                $(document).on('click', '.delete', function() {
                    swal({
                        title: '{{trans("admin.Areyousure")}}',
                        text: '{{trans("admin.Oncedeleted")}}',
                        icon: "warning",
                        buttons: ['{{trans("admin.cancel")}}', '{{trans("admin.ok")}}'],
                        dangerMode: true,
                        })
                        .then((willDelete) => {
                        if (willDelete) {
                            this.closest("tr").remove();
                            swal('{{trans("admin.Deletedsuccessfully")}}', {
                            icon: "success",
                            });
                        } else {
                            /* swal("Your imaginary file is safe!"); */
                        }
                    });

                });

        /* end delete option */
    });
</script>
    <script src="{{asset('assets/libs/summernote/summernote.min.js')}}"></script>

    <!-- Page js-->
    <script src="{{asset('assets/js/pages/form-summernote.init.js')}}"></script>
    <script src="{{asset('assets/libs/dropzone/dropzone.min.js')}}"></script>
    <script src="{{asset('assets/libs/dropify/dropify.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/form-fileuploads.init.js')}}"></script>
@endsection
