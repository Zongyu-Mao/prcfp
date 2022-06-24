<?php

namespace App\Notifications\Examination\ExamDiscussion;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\Exam;

class ExamOpponentCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamOpponent $examOpponent)
    {
        $this->examOpponent = $examOpponent;
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

    // 词条的反对意见创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $exam = Exam::find($this->examOpponent->exam_id);
        $link = '/examination/discussion/'.$exam->id.'/'.$exam->title.'#opponent'.$this->examOpponent->id;
        return [
            'creator_id'    => $this->examOpponent->author_id,
            'creator'       => $this->examOpponent->author,
            'link'          => $link,
            'matter'        => '你关注（协作）的试卷《'.$exam->title.'》新增[反对]立场讨论内容<'.$this->examOpponent->title.'>。'
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
