<?php

namespace App\Notifications\Examination\ExamResort;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamResort\ExamResortSupportComment;
use App\Home\Examination\ExamResort;
use App\Home\Examination\Exam;

class ExamResortSupportCommentCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamResortSupportComment $examResortSupportComment)
    {
        $this->examResortSupportComment = $examResortSupportComment;
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
        $comment = $this->examResortSupportComment;
        
        $resort = ExamResort::find($comment->resortId);
        $exam = Exam::find($resort->exam_id);
        $link = '/examination/resort/'.$exam->id.'/'.$exam->title.'#comment'.$comment->id;
        if($comment->pid == 0){
            $matter = $comment->getAuthor->username.'回复了你在试卷《'.$exam->title.'》发布的帮助内容<'.$resort->title.'>。';
        }else{
            $matter = $comment->getAuthor->username.'回复了你在试卷《'.$exam->title.'》帮助内容发布的留言：<'.ExamResortSupportComment::find($comment->pid)->title.'>。';
        }
        
        return [
            'creator_id'    => $comment->author_id,
            'creator'       => $comment->author,
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
