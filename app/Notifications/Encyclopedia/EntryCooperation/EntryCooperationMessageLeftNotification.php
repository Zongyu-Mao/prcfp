<?php

namespace App\Notifications\Encyclopedia\EntryCooperation;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationMessage;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\Entry;

class EntryCooperationMessageLeftNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryCooperationMessage $entryCooperationMessage)
    {
        $this->entryCooperationMessage = $entryCooperationMessage;
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
        $cooperation = EntryCooperation::find($this->entryCooperationMessage->cooperation_id);
        $entry = Entry::find($cooperation->eid);
        $link = '/encyclopedia/cooperation/'.$entry->id.'/'.$entry->title.'#cooperationMessage'.$this->entryCooperationMessage->id;
        return [
            'creator_id'    => $this->entryCooperationMessage->author_id,
            'creator'       => $this->entryCooperationMessage->author,
            'link'          => $link,
            'matter'        => '协作词条《'.$entry->title.'》的协作计划<'.$cooperation->title.'>新增留言信息<'.$this->entryCooperationMessage->title.'>。'
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
