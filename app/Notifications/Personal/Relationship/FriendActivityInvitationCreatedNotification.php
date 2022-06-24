<?php

namespace App\Notifications\Personal\Relationship;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Personal\Relationship\FriendActivityInvitationRecord;

class FriendActivityInvitationCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FriendActivityInvitationRecord $friendActivityInvitationRecord)
    {
        $this->friendActivityInvitationRecord = $friendActivityInvitationRecord;
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
        $user_id = $this->friendActivityInvitationRecord->user_id;
        $username = $this->friendActivityInvitationRecord->username;
        $subject = $this->friendActivityInvitationRecord->subject;
        $link = '/personal/privateLetter#friendActivityInvitation'.$this->friendActivityInvitationRecord->id;
        return [
            'creator_id'    => $user_id,
            'creator'       => $username,
            'link'          => $link,
            'matter'        => $username.'邀请你'.$subject.'。'
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
