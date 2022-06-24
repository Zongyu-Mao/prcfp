<?php

namespace App\Notifications\Encyclopedia\EntryDebate\EntryDebateClear;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\Entry;

class EntryDebateTimeOutByRefereeClearedToRefereeNotification extends Notification
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
        $matter = '你裁判的攻辩<'.$this->entryDebate->title.'>由于你结算超时，已经由系统自动结算。非常遗憾，你将受到系统惩罚，感谢参与！[来自词条：《'.$entry->title.'》]。';
        $creator = $this->entryDebate->referee;
        $creator_id = $this->entryDebate->referee_id;
        return [
            'creator_id'    => $creator_id,
            'creator'       => $creator,
            'link'          => $link,
            'matter'        => $matter
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
