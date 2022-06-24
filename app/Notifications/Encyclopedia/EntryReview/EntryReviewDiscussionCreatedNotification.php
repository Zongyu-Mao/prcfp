<?php

namespace App\Notifications\Encyclopedia\EntryReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryReview\EntryReviewDiscussion;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\Entry;

class EntryReviewDiscussionCreatedNotification extends Notification
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

    // 演示评审计划创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $entryReview = EntryReview::find($this->entryReviewDiscussion->rid);
        $entry = Entry::find($entryReview->eid);
        $link = '/encyclopedia/review/'.$entry->id.'/'.$entry->title.'#reviewDiscussion'.$this->entryReviewDiscussion->id;
        if($this->entryReviewDiscussion->standpoint == '1'){
            $matter = '协作词条《'.$entry->title.'》的评审计划<'.$entryReview->title.'>新增支持意见。';
        }elseif($this->entryReviewDiscussion->standpoint == '3'){
            $matter = '协作词条《'.$entry->title.'》的评审计划<'.$entryReview->title.'>新增中立意见。';
        }
        return [
            'creator_id'    => $this->entryReviewDiscussion->author_id,
            'creator'       => $this->entryReviewDiscussion->author,
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
