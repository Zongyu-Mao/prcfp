<?php

namespace App\Notifications\Encyclopedia\EntryResort;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\Entry;

class EntryResortSupportCreatedNotification extends Notification
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

    // 演示评审计划创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $entry = Entry::find($this->entryResort->eid);
        $parentResort = EntryResort::find($this->entryResort->pid);
        $link = '/encyclopedia/resort/'.$entry->id.'/'.$entry->title.'#resort'.$this->entryResort->id;
        return [
            'creator_id'    => $this->entryResort->author_id,
            'creator'       => $this->entryResort->author,
            'link'          => $link,
            'matter'        => '你在词条《'.$entry->title.'》发布的求助内容<'.$parentResort->title.'>新增了帮助内容：<'.$this->entryResort->title.'>。'
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
