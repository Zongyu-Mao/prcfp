<?php

namespace App\Notifications\Encyclopedia\EntryReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryReview\EntryReviewAdvise;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\Entry;

class EntryReviewAdvisementRejectedToUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryReviewAdvise $entryReviewAdvise)
    {
        $this->entryReviewAdvise = $entryReviewAdvise;
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

    public function toDatabase($notifiable)
    {
        $entryReview = EntryReview::find($this->entryReviewAdvise->rid);
        $entry = Entry::find($entryReview->eid);
        $parentAdvise = EntryReviewAdvise::find($this->entryReviewAdvise->pid);
        $link = '/encyclopedia/review/'.$entry->id.'/'.$entry->title.'#reviewAdvise'.$this->entryReviewAdvise->id;
        return [
            'creator_id'    => $this->entryReviewAdvise->author_id,
            'creator'       => $this->entryReviewAdvise->author,
            'link'          => $link,
            'matter'        => '你在词条《'.$entry->title.'》的评审计划<'.$entryReview->title.'>中的建议<'.$parentAdvise->title.'>已被拒绝，理由：<'.$this->entryReviewAdvise->title.'>。'
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
