<?php

namespace App\Notifications\Encyclopedia\EntryResort;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\Entry;

class EntryResortCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryResort $entryResort)
    {
        $this->entryResort = $entryResort;
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

    // 求助创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $entry = Entry::find($this->entryResort->eid);
        $link = '/encyclopedia/resort/'.$entry->id.'/'.$entry->title;
        return [
            'creator_id'    => $this->entryResort->author_id,
            'creator'       => $this->entryResort->author,
            'link'          => $link,
            'matter'        => '你关注（专业）的词条《'.$entry->title.'》新增求助内容<'.$this->entryResort->title.'>。'
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
