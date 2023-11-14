<?php

namespace App\Http\Controllers\Admin;
use App\Models\Shop;
use App\Models\User;
use App\Models\Manager;
use App\Models\DeliveryBoy;
use App\Models\ShopRevenue;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FCMController;
use Illuminate\Support\Facades\Notification;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use App\Notifications\SendGeneralFromAdminNotification;
use App\Notifications\Fairbase\GeneralFromAdminNotificationFcm;

class NotificationController extends Controller
{
    use GeneralFromAdminNotificationFcm;
    private $manager;

    private $deliveryBoy;
    private $user;
    public function __construct(Manager $manager, DeliveryBoy $deliveryBoy,User $user)
    {
        $this->manager = $manager;
        $this->deliveryBoy = $deliveryBoy;
        $this->user = $user;
    }


    public function index()
    {
        $admin = Auth::guard('admin')->user();
        //dd($user->notifications()->limit(10)->get());
        if($admin){
            $notifications = $admin->notifications()->paginate(10);
            $newCount = $admin->unreadNotifications()->count();
        }
       // dd( $notifications);
        return view('admin.notifications.index',compact('notifications','newCount'));

    }


    public function create()
    {
        $managers = $this->manager->all();
        $deliveryBoys = $this->deliveryBoy->all();
        $users = $this->user->all();
        return view('admin.notifications.create',compact('managers','deliveryBoys','users'));
       // return view('admin.notifications.create-notification');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction ();
            //dd($request->all());
        $data = [
            'en'=>[
                'title'=>$request->title['en'],
                'body'=>$request->body['en']
            ],
            'ar'=>[
                'title'=>$request->title['ar'],
                'body'=>$request->body['ar']
            ],
            'title'=>[
                'en'=>$request->title['en'],
                'ar'=>$request->title['ar']
            ],
            'body'=>[
                'en'=>$request->body['en'],
                'ar'=>$request->body['ar']
            ],
            'titlemsg'=> 'title rahaf',
            'bodymsg'=> 'body rahaf',
            'icon'=>asset('assets/images/logo-light.png'),
            'url'=>$request->url,
        ];
    //  dd($data);
        if($request->type == "all"){
            $managers = $this->manager->all();
            Notification::send($managers,new SendGeneralFromAdminNotification($data));
            $this->SendGeneralFromAdminNotificationFcm($data, 'allManagers', $managers );

            $deliveryBoys = $this->deliveryBoy->all();
            Notification::send($deliveryBoys,new SendGeneralFromAdminNotification($data));
            $this->SendGeneralFromAdminNotificationFcm($data, 'allDeliveryBoys', $deliveryBoys );

            $users = $this->user->all();
            Notification::send($users,new SendGeneralFromAdminNotification($data));
            $this->SendGeneralFromAdminNotificationFcm($data, 'allUsers', $users );
            DB::commit();
            return redirect()->route('admin.notifications.index')->with('success',trans('all.Notification-sent-successfully'));

        }elseif($request->type == "allManagers"){
            $to = $this->manager->all();
            Notification::send($to,new SendGeneralFromAdminNotification($data));
            $this->SendGeneralFromAdminNotificationFcm($data, $request->type );
        }  elseif($request->type == "allDeliveryBoys"){
            $to = $this->deliveryBoy->all();
            Notification::send($to,new SendGeneralFromAdminNotification($data));
            $this->SendGeneralFromAdminNotificationFcm($data, $request->type );
        } elseif($request->type == "allUsers"){
            $to = $this->user->all();
            Notification::send($to,new SendGeneralFromAdminNotification($data));
            $this->SendGeneralFromAdminNotificationFcm($data, $request->type );
        }  elseif($request->type == "specific-manager"){
            $to = $this->manager->where('id', $request->manager_id)->get();
            $selected_user = $request->manager_id;
            Notification::send($to,new SendGeneralFromAdminNotification($data));
            $this->SendGeneralFromAdminNotificationFcm($data, $request->type, $selected_user );

        } elseif($request->type == "specific-delivery-boy"){
            $to = $this->deliveryBoy->where('id', $request->deliveryBoy_id)->get();
            $selected_user = $request->deliveryBoy_id;
            Notification::send($to,new SendGeneralFromAdminNotification($data));
            $this->SendGeneralFromAdminNotificationFcm($data, $request->type, $selected_user );
        } elseif($request->type == "specific-user"){
            $to = $this->user->where('id', $request->user_id)->get();
            $selected_user = $request->user_id;
            Notification::send($to,new SendGeneralFromAdminNotification($data));
            $this->SendGeneralFromAdminNotificationFcm($data, $request->type, $selected_user );
        } else {
            return redirect()->route('admin.notifications.index')->with('error',trans('all.No recipient has been specified'));
        }

            DB::commit();
            return redirect()->route('admin.notifications.index')->with('success',trans('all.Notification-sent-successfully'));
        }catch(\Exception $e){
            Log::info($e->getMessage());
            DB::rollBack();
            //  return $e->getMessage();
            return redirect()->route('admin.categories.create')->with(['error' => 'Something wrong']);
        }

    }

    public function show($id)
    {


    }


    public function edit($id)
    {

    }


    public function update(Request $request, $id){

    }


    public function destroy($id)
    {

    }


    public function send(Request $request){

        $this->validate($request,[
            'title'=>'required',
            'body'=>'required'
        ]);

        $response = FCMController::sendMessageToAll($request->title,$request->body);
        //return $response->success;
        if($response){
            return redirect()->back()->with('message', 'Notification sent');
        }else{
            return redirect()->back()->with('error', 'Notification was not sent');
        }
    }
}
