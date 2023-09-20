<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\User;

class CodeController extends Controller
{
    public function index(){
        $shop = auth()->user()->shop;
     $codes=Code::with('user')->paginate(10);
     return view('manager.codes.codes',compact('codes'));
    }

     public function create(){
        $shop = auth()->user()->shop;
        $users=User::get();
        return view('manager.codes.create-code',compact('users','shop'));
    }

      public function store(Request $request){
        $codes = new Code();
        $codes->title = $request->get('title');
        $codes->max_use = $request->get('max_use');
        $codes->user_id = $request->get('user');


        if ($codes->save()) {
            return redirect(route('manager.codes.index'))->with([
                'message' => 'codes has been created'
            ]);
        } else {
            return redirect(route('manager.codes.index'))->with([
                'error' => 'Something wrong'
            ]);
        }

    }


     public function edit($id){

        $code=Code::findOrFail($id);
        $users=User::get();
        return view('manager.codes.edit-code',compact('code','users'));
    }

    public function update(Request $request,$id){

        $codes = Code::findOrFail($id);

        $codes->title = $request->get('title');
        $codes->max_use = $request->get('max_use');

        if ($codes->save()) {
            return redirect()->back()->with([
                'message' => 'codes has been updated'
            ]);
        } else {
            return redirect()->back()->with([
                'error' => 'Something wrong'
            ]);
        }

    }
}
