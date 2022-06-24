<?php

namespace App\Notifications\Encyclopedia\EntryDiscussion;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\Entry;

class EntryOpponentRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryOpponent $entryOpponent)
    {
        $this->entryOpponent = $entryOpponent;
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
        $entry = Entry::find($this->entryOpponent->eid);
        $pOpponent = EntryOpponent::find($this->entryOpponent->pid)->title;
        $link = '/encyclopedia/discussion/'.$entry->id.'/'.$entry->title.'#opponent'.$this->entryOpponent->id;
        return [
            'creator_id'    => $this->entryOpponent->author_id,
            'creator'       => $this->entryOpponent->author,
            'link'          => $link,
            'matter'        => '你关注（协作）的词条《'.$entry->title.'》的[反对]立场讨论内容<'.$pOpponent.'>已被拒绝，理由：<'.$this->entryOpponent->title.'>。'
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
