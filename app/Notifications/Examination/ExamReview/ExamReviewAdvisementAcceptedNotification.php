<?php

namespace App\Notifications\Examination\ExamReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamReview\ExamReviewAdvise;
use App\Home\Examination\ExamReview;
use App\Home\Examination\Exam;

class ExamReviewAdvisementAcceptedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamReviewAdvise $examReviewAdvise)
    {
        $this->examReviewAdvise = $examReviewAdvise;
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
        $advise = $this->examReviewAdvise;
        $examReview = ExamReview::find($advise->rid);
        $exam = Exam::find($examReview->exam_id);
        $link = '/examination/review/'.$exam->id.'/'.$exam->title.'#advise'.$advise->id;
        return [
            'creator_id'    => $advise->recipient_id,
            'creator'       => $advise->recipient,
            'link'          => $link,
            'matter'        => '你在试卷《'.$exam->title.'》的评审计划<'.$examReview->title.'>中提出的建议<'.$advise->title.'>已被接受。'
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
