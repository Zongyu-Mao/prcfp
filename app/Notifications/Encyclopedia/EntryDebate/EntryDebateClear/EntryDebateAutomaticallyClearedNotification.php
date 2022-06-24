<?php

namespace App\Notifications\Encyclopedia\EntryDebate\EntryDebateClear;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\Entry;

class EntryDebateAutomaticallyClearedNotification extends Notification
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
    // 辩论结算成功后通知相关用户
    public function toDatabase($notifiable)
    {
        $entry = Entry::find($this->entryDebate->eid);
        $link = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$this->entryDebate->type.'&type_id='.$this->entryDebate->type_id;
        // $doer = User::find($this->entryDebate->Aauthor_id);
        if($this->entryDebate->victory == '1'){
            $matter = '攻辩<'.$this->entryDebate->title.'>已经结算。攻方['.$this->entryDebate->Aauthor.']胜出。[来自词条：《'.$entry->title.'》]。';
            $creator = $this->entryDebate->Aauthor;
            $creator_id = $this->entryDebate->Aauthor_id;
        }elseif($this->entryDebate->victory == '2'){
            $matter = '攻辩<'.$this->entryDebate->title.'>已经结算。攻方辩方['.$this->entryDebate->Bauthor.']胜出。[来自词条：《'.$entry->title.'》]。';
            $creator = $this->entryDebate->Bauthor;
            $creator_id = $this->entryDebate->Bauthor_id;
        }
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
