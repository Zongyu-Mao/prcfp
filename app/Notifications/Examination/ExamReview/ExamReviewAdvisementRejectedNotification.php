<?php

namespace App\Notifications\Examination\ExamReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamReview\ExamReviewAdvise;
use App\Home\Examination\ExamReview;
use App\Home\Examination\Exam;

class ExamReviewAdvisementRejectedNotification extends Notification
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

    public function toDatabase($notifiable)
    {
        $advise = $this->examReviewAdvise;
        $examReview = ExamReview::find($advise->rid);
        $exam = Exam::find($examReview->exam_id);
        $parentAdvise = ExamReviewAdvise::find($advise->pid);
        $link = '/examination/review/'.$exam->id.'/'.$exam->title.'#advise'.$advise->id;
        return [
            'creator_id'    => $advise->author_id,
            'creator'       => $advise->author,
            'link'          => $link,
            'matter'        => '协作试卷《'.$exam->title.'》的评审计划<'.$examReview->title.'>中的建议<'.$parentAdvise->title.'>已被拒绝，理由：<'.$advise->title.'>。'
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
