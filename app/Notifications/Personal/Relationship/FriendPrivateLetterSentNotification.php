<?php

namespace App\Notifications\Personal\Relationship;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Personal\Relationship\FriendPrivateLetter;

class FriendPrivateLetterSentNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FriendPrivateLetter $friendPrivateLetter)
    {
        $this->friendPrivateLetter = $friendPrivateLetter;
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
        $l = $this->friendPrivateLetter;
        $link = '/personal/private-letter#private-letter'.$l->id;
        return [
            'creator_id'    => $l->from_id,
            'creator'       => $l->from_username,
            'link'          => $link,
            'matter'        => $l->from_username.'给你发送了好友私信：<'.$l->title.'>。'
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
