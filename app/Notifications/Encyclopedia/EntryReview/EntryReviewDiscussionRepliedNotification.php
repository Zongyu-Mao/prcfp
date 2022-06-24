<?php

namespace App\Notifications\Encyclopedia\EntryReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryReview\EntryReviewDiscussion;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\Entry;

class EntryReviewDiscussionRepliedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryReviewDiscussion $entryReviewDiscussion)
    {
        $this->entryReviewDiscussion = $entryReviewDiscussion;
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

    // 评论成功后对员评论作者的通知
    public function toDatabase($notifiable)
    {
        $entryReview = EntryReview::find($this->entryReviewDiscussion->rid);
        $entry = Entry::find($entryReview->eid);
        $link = '/encyclopedia/review/'.$entry->id.'/'.$entry->title.'#reviewDiscussion'.$this->entryReviewDiscussion->id;
        return [
            'creator_id'    => $this->entryReviewDiscussion->author_id,
            'creator'       => $this->entryReviewDiscussion->author,
            'link'          => $link,
            'matter'        => '你在词条《'.$entry->title.'》的评审计划<'.$entryReview->title.'>中发表的评论已经被回复。'
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
