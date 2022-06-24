<?php

namespace App\Notifications\Personal\Relationship;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Personal\Relationship\UserFriendApplicationRecord;

class FriendApplicationCompletedNotification extends Notification
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

    // 用户同意好友添加后对用户的通知
    public function toDatabase($notifiable)
    {
        $user_id = $this->userFriendApplicationRecord->user_id;
        $username = $this->userFriendApplicationRecord->username;
        $link = '/personal/'.$this->userFriendApplicationRecord->application_id;
        return [
            'creator_id'    => $user_id,
            'creator'       => $username,
            'link'          => $link,
            'matter'        => '你同意了'.$this->userFriendApplicationRecord->application_username.'好友申请，现在你们已经成为好友。'
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
