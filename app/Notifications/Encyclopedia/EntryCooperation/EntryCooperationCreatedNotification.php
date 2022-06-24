<?php

namespace App\Notifications\Encyclopedia\EntryCooperation;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\Entry;

class EntryCooperationCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryCooperation $entryCooperation)
    {
        $this->entryCooperation = $entryCooperation;
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

    // 评审计划创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $entry = Entry::find($this->entryCooperation->eid);
        $link = '/encyclopedia/cooperation/'.$entry->id.'/'.$entry->title;
        return [
            'creator_id'    => $this->entryCooperation->creator_id,
            'creator'       => $this->entryCooperation->creator,
            'link'          => $link,
            'matter'        => '你关注（专业）的词条《'.$entry->title.'》的协作计划<'.$this->entryCooperation->title.'>已经创建。'
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
