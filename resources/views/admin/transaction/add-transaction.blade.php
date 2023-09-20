@extends('admin.layouts.app', ['title' => 'Add Transactions'])

@section('css')
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ env('APP_NAME') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.transactions.index') }}">{{ __('admin.transaction') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('admin.create') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('admin.add Payment') }} | {{$item->name}}</h4>
                    {{$item->email}} | {{$item->mobile}}
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.transactions.store',$item->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                        <div class="col-12 ">
                                            <div class="card">
                                                <div class="card-body">
                                                
                                                    <div class="row">
                                                        <input name='type' value='{{$type}}' hidden> 
                                                        <div class="col-12 col-lg-6">
                                                            <div class="form-group mt-0">
                                                                <label for="name">{{ __('admin.from_date') }} <span class="text-danger">*</span> </label>
                                                                <input  type="text"
                                                                    class="form-control @if ($errors->has('from_date')) is-invalid @endif"
                                                                    id="from_date" placeholder="{{ __('admin.from_date') }}"
                                                                    name="from_date" value="{{ old('from_date') }}" autocomplete="off">
                                                                @if ($errors->has('from_date'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('from_date') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-12 col-lg-6">
                                                            <div class="form-group mt-0">
                                                                <label for="email">{{ __('admin.to_date') }} </label>
                                                                <input type="text"
                                                                    class="form-control @if ($errors->has('to_date')) is-invalid @endif"
                                                                    id="to_date" placeholder="{{ __('admin.to_date') }}"
                                                                    name="to_date" value="{{ old('to_date') }}" autocomplete="off" >
                                                                @if ($errors->has('to_date'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('to_date') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        
                                                        
                                                         <div class="col-12 col-lg-6">
                                                            <div class="form-group mt-0">
                                                                <label for="status">{{ __('admin.status') }} <span class="text-danger">*</span></label>
                                                                <select name='status' class="form-control">
                                                                    <option value='paid'>{{__('admin.paid')}}</option>
                                                                    <option value='requested'>{{__('admin.requested')}}</option>
                                                                    <option value='cancelled'>{{__('admin.cancelled')}}</option>

                                                                </select>
                                                                @if ($errors->has('status'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('status') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>  
                                                                <div class="col-12 col-lg-6">
                                                            <div class="form-group mt-4"> <div id='total'></div>
                                                             @if ($errors->has('total'))
                                                                    <span style="color:red">
                                                                        <strong>{{ $errors->first('total') }}</strong>
                                                                    </span>
                                                                @endif
                                                </div></div>
                                                
                                                </div>

                                                   
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 text-right">
                                            <button type="submit"
                                                class="btn btn-success waves-effect waves-light mr-1">{{ __('admin.save') }}
                                            </button>
                                        </div>
                                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
          <div class="card">
            <div class="card-body">
                @if($transactions->count()>0)
                    <div class="row justify-content-between mx-1">
                        <h4>{{__('admin.payment')}}</h4>
                        
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-centered table-bordered  table-nowrap table-hover mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">{{__('admin.amount')}}</th>
                                        <th class="text-center">{{__('admin.from_date')}}</th>
                                        <th class="text-center">{{__('admin.to_date')}}</th>
                                        <th class="text-center">{{__('admin.status')}}</th>
                                    </tr>

                                    </thead>
                                   <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td class="text-center">
                                                <a 
                                                   class="font-weight-semibold"># {{ $transaction->id }}</a>
                                            </td>
                                            <td class="text-center">
                                                {{\App\Helpers\AppSetting::$currencySign}} {{\App\Helpers\CurrencyUtil::doubleToString($transaction->total)}}
                                            </td>

                                            <td class="text-center">{{date('D' , strtotime($transaction->from_date))}} | {{date('Y-m-d' , strtotime($transaction->from_date))}}</td>
                                            <td class="text-center">{{date('D' , strtotime($transaction->to_date))}} | {{date('Y-m-d' , strtotime($transaction->to_date))}}</td>
                                            <td class="text-center"> {{__('admin.'.$transaction->status)}}</td>
     
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body">
                            <h3>{{__('admin.there_is_no_any_revenues_yet')}}</h3>
                        </div>
                    </div>
                @endif
            </div>
        </div>
@endsection


@section('script') 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    function getFromDate(to_date){
     var from_date = document.getElementById("from_date").value;
         $.ajax({
             type:'GET',
             url:'/admin/transactions/get_total/{{$item->id}}',
             data: { from_date: from_date,
                        to_date: to_date,
                        type: '{{$type}}'
             },
             success:function(response) {
                 $('#total').empty();
                 $('#total').append('<input hidden name="total" value="'+response+'"><h3>Total = '+response+' JOD</h3>');
             }
         });

    }
        $( "#from_date" ).datepicker({
            maxDate:0
        });
        $( "#to_date" ).datepicker({
                 maxDate:0,
                onSelect: function(to_date) {
                getFromDate(to_date);
            //do your processing here
          }
 });
        
    </script> 

@endsection
