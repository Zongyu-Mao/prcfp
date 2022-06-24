<?php

namespace App\Notifications\Examination\ExamDiscussion;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamDiscussion;
use App\Home\Examination\Exam;

class ExamDiscussionRepliedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamDiscussion $examDiscussion)
    {
        $this->examDiscussion = $examDiscussion;
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
    
    // 反对意见创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $exam = Exam::find($this->examDiscussion->exam_id);
        $parentDiscussion = ExamDiscussion::find($this->examDiscussion->pid);
        $link = '/examination/discussion/'.$exam->id.'/'.$exam->title.'#discussion'.$this->examDiscussion->id;
        return [
            'creator_id'    => $this->examDiscussion->author_id,
            'creator'       => $this->examDiscussion->author,
            'link'          => $link,
            'matter'        => '你在试卷《'.$exam->title.'》的[普通]讨论内容<'.$parentDiscussion->title.'>已被回复，内容：<'.$this->examDiscussion->title.'>。'
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
