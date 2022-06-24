<?php

namespace App\Notifications\Examination\ExamReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamReview\ExamReviewOpponent;
use App\Home\Examination\ExamReview;
use App\Home\Examination\Exam;

class ExamReviewOpponentRejectedToUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamReviewOpponent $examReviewOpponent)
    {
        $this->examReviewOpponent = $examReviewOpponent;
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
        $opponent = $this->examReviewOpponent;
        $examReview = ExamReview::find($opponent->rid);
        $exam = Exam::find($examReview->exam_id);
        $parentOpponent = ExamReviewOpponent::find($opponent->pid);
        $link = '/examination/review/'.$exam->id.'/'.$exam->title.'#opponent'.$opponent->id;
        return [
            'creator_id'    => $opponent->author_id,
            'creator'       => $opponent->author,
            'link'          => $link,
            'matter'        => '你在试卷《'.$exam->title.'》评审计划<'.$examReview->title.'>中的反对意见<'.$parentOpponent->title.'>已被拒绝，理由：<'.$opponent->title.'>。'
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
