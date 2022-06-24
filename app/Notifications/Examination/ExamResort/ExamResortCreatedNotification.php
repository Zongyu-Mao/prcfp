<?php

namespace App\Notifications\Examination\ExamResort;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamResort;
use App\Home\Examination\Exam;

class ExamResortCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamResort $examResort)
    {
        $this->examResort = $examResort;
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

    // 求助创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $exam = Exam::find($this->examResort->exam_id);
        $link = '/examination/resort/'.$exam->id.'/'.$exam->title;
        return [
            'creator_id'    => $this->examResort->author_id,
            'creator'       => $this->examResort->author,
            'link'          => $link,
            'matter'        => '你关注（专业）的试卷《'.$exam->title.'》新增求助内容<'.$this->examResort->title.'>。'
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
