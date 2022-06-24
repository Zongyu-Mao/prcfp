<?php

namespace App\Notifications\Encyclopedia\Entry;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\Entry;
use App\Models\User;

class EntryCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
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

    // 演示词条创建成功后通知作者创建成功
    public function toDatabase($notifiable)
    {
        $link = '/encyclopedia/reading/'.$this->entry->id.'/'.$this->entry->title;
        $doer = User::find($this->entry->creator_id);
        return [
            'creator_id'    => $doer->id,
            'creator'       => $doer->username,
            'link'          => $link,
            'matter'        => '你的词条《'.$this->entry->title.'》创建成功。'
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
