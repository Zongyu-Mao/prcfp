<?php

namespace App\Notifications\Encyclopedia\EntryDiscussion;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryDiscussion;
use App\Home\Encyclopedia\Entry;

class EntryDiscussionCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryDiscussion $entryDiscussion)
    {
        $this->entryDiscussion = $entryDiscussion;
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

    // 词条的反对意见创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $entry = Entry::find($this->entryDiscussion->eid);
        $link = '/encyclopedia/discussion/'.$entry->id.'/'.$entry->title.'#discussion'.$this->entryDiscussion->id;
        return [
            'creator_id'    => $this->entryDiscussion->author_id,
            'creator'       => $this->entryDiscussion->author,
            'link'          => $link,
            'matter'        => '你协作的词条《'.$entry->title.'》新增[普通]讨论内容<'.$this->entryDiscussion->title.'>。'
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
