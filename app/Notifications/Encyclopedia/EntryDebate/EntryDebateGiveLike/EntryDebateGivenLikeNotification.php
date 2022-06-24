<?php

namespace App\Notifications\Encyclopedia\EntryDebate\EntryDebateGiveLike;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryDebate\EntryDebateStarRecord;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\Entry;

class EntryDebateGivenLikeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryDebateStarRecord $entryDebateStarRecord)
    {
        $this->entryDebateStarRecord = $entryDebateStarRecord;
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
        $debate = EntryDebate::find($this->entryDebateStarRecord->debate_id);
        $entry = Entry::find($debate->eid);
        // 判断立场
        if($this->entryDebateStarRecord->star == '0'){
            $standpoint = '送了一颗红星星给';
        }elseif($this->entryDebateStarRecord->star == '1'){
            $standpoint = '送了一颗黑星星给';
        }
        
        $link = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$this->entryDebate->type.'&type_id='.$this->entryDebate->type_id;
        // $doer = User::find($this->entryDebate->Aauthor_id);
        return [
            'creator_id'    => $this->entryDebateStarRecord->user_id,
            'creator'       => $this->entryDebateStarRecord->username,
            'link'          => $link,
            'matter'        => '用户['.$this->entryDebateStarRecord->username.']'.$standpoint.'你，在你属的攻辩：<'.$debate->title.'>。[来自词条：《'.$entry->title.'》]。'
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
