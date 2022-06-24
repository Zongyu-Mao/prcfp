<?php

namespace App\Notifications\Encyclopedia\EntryDebate;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\Entry;
use Carbon\Carbon;

class EntryDebateAClosingStatementCreatedNotification extends Notification
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
        $debate = $this->entryDebate;
        $entry = Entry::find($debate->eid);
        $time = Carbon::parse($debate->ACScreateTime)->addDays(5);
        $link = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        // $doer = User::find($this->entryDebate->Aauthor_id);
        return [
            'creator_id'    => $this->entryDebate->Aauthor_id,
            'creator'       => $this->entryDebate->Aauthor,
            'link'          => $link,
            'matter'        => '对方['.$this->entryDebate->Aauthor.']已经在攻辩：<'.$this->entryDebate->title.'>发表攻方的总结陈词。你可以在'.$time.'前回复。[来自词条：《'.$entry->title.'》]。'
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
