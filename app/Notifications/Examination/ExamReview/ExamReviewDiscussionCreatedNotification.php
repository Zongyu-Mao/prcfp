<?php

namespace App\Notifications\Examination\ExamReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamReview\ExamReviewDiscussion;
use App\Home\Examination\ExamReview;
use App\Home\Examination\Exam;

class ExamReviewDiscussionCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamReviewDiscussion $examReviewDiscussion)
    {
        $this->examReviewDiscussion = $examReviewDiscussion;
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
        $discussion = $this->examReviewDiscussion;
        $examReview = ExamReview::find($discussion->rid);
        $exam = Exam::find($examReview->exam_id);
        $link = '/examination/review/'.$exam->id.'/'.$exam->title.'#discussion'.$discussion->id;
        if($discussion->standpoint == '1'){
            $matter = '协作试卷《'.$exam->title.'》的评审计划<'.$examReview->title.'>新增支持意见。';
        }elseif($discussion->standpoint == '3'){
            $matter = '协作试卷《'.$exam->title.'》的评审计划<'.$examReview->title.'>新增中立意见。';
        }
        return [
            'creator_id'    => $discussion->author_id,
            'creator'       => $discussion->author,
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
