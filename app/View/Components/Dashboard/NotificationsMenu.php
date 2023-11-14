<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class NotificationsMenu extends Component
{
    public $notifications;
    public $newCount;
    public $user;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct() 
    {
        $user = Auth::guard('admin')->user();
        //dd($user->notifications()->limit(10)->get());
        if($user){
            $this->notifications = $user->notifications()->limit(10)->get();
            $this->newCount = $user->unreadNotifications()->count();
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.notifications-menu');
    }
}
