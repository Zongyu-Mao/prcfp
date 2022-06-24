<?php

namespace App\Notifications\Encyclopedia\EntryDebate;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\Entry;

class EntryDebateBClosingStatementCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryDebate $entryDebate)
    {
        $this->entryDebate = $entryDebate;
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

    // 词条辩论创建成功后通知相关用户
    public function toDatabase($notifiable)
    {
        $entry = Entry::find($this->entryDebate->eid);
        $link = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$this->entryDebate->type.'&type_id='.$this->entryDebate->type_id;
        // $doer = User::find($this->entryDebate->Aauthor_id);
        return [
            'creator_id'    => $this->entryDebate->Bauthor_id,
            'creator'       => $this->entryDebate->Bauthor,
            'link'          => $link,
            'matter'        => '对方['.$this->entryDebate->Bauthor.']已经在你发起的攻辩：<'.$this->entryDebate->title.'>发表辩方的总结陈词，攻辩选手流程结束。[来自词条：《'.$entry->title.'》]。'
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
