<?php

namespace App\Notifications\Examination\ExamResort;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamResort;
use App\Home\Examination\Exam;

class ExamResortSupportAcceptedNotification extends Notification
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

    // 演示评审计划创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $exam = Exam::find($this->examResort->exam_id);
        $parentResort = ExamResort::find($this->examResort->pid);
        $link = '/examination/resort/'.$exam->id.'/'.$exam->title.'#resort'.$this->examResort->id;
        return [
            'creator_id'    => $parentResort->author_id,
            'creator'       => $parentResort->author,
            'link'          => $link,
            'matter'        => '协作试卷《'.$exam->title.'》的求助内容<'.$parentResort->title.'>已经解决。'
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
