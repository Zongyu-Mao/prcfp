<?php

namespace App\Notifications\Encyclopedia\EntryReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryReview\EntryReviewOpponent;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\Entry;

class EntryReviewOpponentRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryReviewOpponent $entryReviewOpponent)
    {
        $this->entryReviewOpponent = $entryReviewOpponent;
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

    // 评审计划拒绝反对意见后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $entryReview = EntryReview::find($this->entryReviewOpponent->rid);
        $entry = Entry::find($entryReview->eid);
        $parentOpponent = EntryReviewOpponent::find($this->entryReviewOpponent->pid);
        $link = '/encyclopedia/review/'.$entry->id.'/'.$entry->title.'#reviewOpponent'.$this->entryReviewOpponent->id;
        return [
            'creator_id'    => $this->entryReviewOpponent->author_id,
            'creator'       => $this->entryReviewOpponent->author,
            'link'          => $link,
            'matter'        => '协作词条《'.$entry->title.'》的评审计划<'.$entryReview->title.'>中的反对意见<'.$parentOpponent->title.'>已被拒绝，理由：<'.$this->entryReviewOpponent->title.'>。'
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
