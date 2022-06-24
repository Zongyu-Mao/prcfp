<?php

namespace App\Notifications\Management\Role;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Personnel\Role\RoleElectRecord;
use App\Models\User;

class RoleElectUpdated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RoleElectRecord $roleElectRecord)
    {
        $this->roleElectRecord = $roleElectRecord;
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

    public function toDatabase($notifiable)
    {
        $e = $this->roleElectRecord;
        $link = '/management/personal';
        $status = $e->status;
        if($status==1) {
            $s = '已经通过';
        }elseif($status==2) {
            $s = '遗憾未成功';
        }else {
            $s = '遗憾未成功';
        }
        return [
            'creator_id'    => $e->user_id,
            'creator'       => User::find($e->user_id)->username,
            'link'          => $link,
            'matter'        => '你的推举'.$s
        ];
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
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
