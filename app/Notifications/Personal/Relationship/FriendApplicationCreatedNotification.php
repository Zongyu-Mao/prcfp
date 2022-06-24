<?php

namespace App\Notifications\Personal\Relationship;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Personal\Relationship\UserFriendApplicationRecord;

class FriendApplicationCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(UserFriendApplicationRecord $userFriendApplicationRecord)
    {
        $this->userFriendApplicationRecord = $userFriendApplicationRecord;
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

    // 演示评审计划创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $application_id = $this->userFriendApplicationRecord->application_id;
        $application_username = $this->userFriendApplicationRecord->application_username;
        $link = '/personal/privateLetter#friendApplicationLetter'.$this->userFriendApplicationRecord->id;
        return [
            'creator_id'    => $application_id,
            'creator'       => $application_username,
            'link'          => $link,
            'matter'        => '用户'.$application_username.'申请添加你为好友。'
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
