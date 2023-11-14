<?php

namespace App\Notifications;

use App\Models\SubCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubCategoryAddedByShopNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $subCategory;
    public function __construct(SubCategory $subCategory)
    {
        $this->subCategory = $subCategory;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
       // return ['mail'];
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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function todatabase($notifiable)
    {
            $userName = auth()->user()->name;

            if ($this->subCategory->image_url) {
                $img = $this->subCategory->image_url;
            } else {
                $img ='assets/images/logo-light.png';
            }
            
            return [
                    'en'=>[
                        'title'=>'Add a new item',
                        'body'=>"A new item  {$this->subCategory->getTranslations('title')['en']} has been added by {$userName}.",
                    ],
                    'ar'=>[
                        'title'=>'إضافة ايتم جديد',
                        'body'=>"{$userName}تم إضافة ايتم جديد  {$this->subCategory->getTranslations('title')['ar']} من قبل ",
                    ],
                    'icon'=>$img,
                    'url'=>url("/admin/sub_categories/sub-categories-requests"),
            ];
           
    }
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
