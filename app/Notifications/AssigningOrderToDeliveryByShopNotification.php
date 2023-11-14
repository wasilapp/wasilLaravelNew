<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Brozot\LaravelFcm\Messages\Notification As FCM;

class AssigningOrderToDeliveryByShopNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $order;
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }


    public function todatabase($notifiable)
    {
        //return auth()->user()->shop;
            $user = auth()->user()->shop;
            
            if ($user->image_url) {
                $img = $user->image_url;
            } else {
                $img ='assets/images/logo-light.png';
            }
            
            return [
                    'en'=>[
                        'title'=>'new order',
                        'body'=>"A new order has been assigned to you by {$user->getTranslations('name')['en']} shop",
                    ],
                    'ar'=>[
                        'title'=>'طلب جديد',
                        'body'=>"تم اسناد طلب جديد إليك بواسطة متجر {$user->getTranslations('name')['ar']}",
                    ],
                    'icon'=>$img,
                    'data'=>$this->order,
                    'url'=>'',
            ];
           
    }

    
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
