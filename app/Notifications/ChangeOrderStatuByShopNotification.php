<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ChangeOrderStatuByShopNotification extends Notification
{
    use Queueable;

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

            $shop = auth()->user()->shop;

           if ($shop->image_url) {
               $img =  asset( $shop->image_url ) ;
           } else {
               $img ='assets/images/logo-light.png';
           }

            return [
                'en'=>[
                    'title'=>'order',
                    'body'=>"order has been accepted by {$shop->getTranslations('name')['en']} shop",
                ],
                'ar'=>[
                        'title'=>'طلب جديد',
                        'body'=>"تم قبول طلبك  بواسطة متجر {$shop->getTranslations('name')['ar']}",
                ],
                'icon'=>$img,
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
