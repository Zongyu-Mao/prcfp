<?php

namespace App\Notifications\Encyclopedia\EntryDiscussion;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryDiscussion\EntryAdvise;
use App\Home\Encyclopedia\Entry;

class EntryAdvisementRejectedToUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
   public function __construct(EntryAdvise $entryAdvise)
    {
        $this->entryAdvise = $entryAdvise;
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

    // 词条的建议被拒绝后通知原内容作者
    public function toDatabase($notifiable)
    {
        $entry = Entry::find($this->entryAdvise->eid);
        $pAdvisement = EntryAdvise::find($this->entryAdvise->pid)->title;
        $link = '/encyclopedia/discussion/'.$entry->id.'/'.$entry->title.'#advise'.$this->entryAdvise->id;
        return [
            'creator_id'    => $this->entryAdvise->author_id,
            'creator'       => $this->entryAdvise->author,
            'link'          => $link,
            'matter'        => '你在词条《'.$entry->title.'》讨论中的[建议]立场内容<'.$pAdvisement.'>已被拒绝。理由：<'.$this->entryAdvise->title.'>。'
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
