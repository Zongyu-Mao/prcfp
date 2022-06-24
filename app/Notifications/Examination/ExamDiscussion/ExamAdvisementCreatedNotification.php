<?php

namespace App\Notifications\Examination\ExamDiscussion;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamDiscussion\ExamAdvise;
use App\Home\Examination\Exam;

class ExamAdvisementCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamAdvise $examAdvise)
    {
        $this->examAdvise = $examAdvise;
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

    // 著作的反对意见创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $exam = Exam::find($this->examAdvise->exam_id);
        $link = '/examination/discussion/'.$exam->id.'/'.$exam->title.'#advise'.$this->examAdvise->id;
        return [
            'creator_id'    => $this->examAdvise->author_id,
            'creator'       => $this->examAdvise->author,
            'link'          => $link,
            'matter'        => '你关注（协作）的试卷《'.$exam->title.'》新增[建议]立场的讨论内容<'.$this->examAdvise->title.'>。'
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
