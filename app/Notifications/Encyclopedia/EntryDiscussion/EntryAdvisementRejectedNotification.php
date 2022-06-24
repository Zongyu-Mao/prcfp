<?php

namespace App\Notifications\Encyclopedia\EntryDiscussion;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryDiscussion\EntryAdvise;
use App\Home\Encyclopedia\Entry;

class EntryAdvisementRejectedNotification extends Notification
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

    // 词条的反对意见被拒绝后通知原内容作者
    public function toDatabase($notifiable)
    {
        $entry = Entry::find($this->entryAdvise->eid);
        $pAdvisement = EntryAdvise::find($this->entryAdvise->pid)->title;
        $link = '/encyclopedia/discussion/'.$entry->id.'/'.$entry->title.'#advise'.$this->entryAdvise->id;
        return [
            'creator_id'    => $this->entryAdvise->author_id,
            'creator'       => $this->entryAdvise->author,
            'link'          => $link,
            'matter'        => '你协作的词条《'.$entry->title.'》的[建议]立场讨论内容<'.$pAdvisement.'>已被小组成员拒绝，理由：<'.$this->entryAdvise->title.'>。'
        ];
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
