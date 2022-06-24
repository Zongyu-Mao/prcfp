<?php

namespace App\Notifications\Examination\ExamDebate\ExamDebateComment;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamDebate\ExamDebateComment;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamDebate;

class ExamDebateCommentRepliedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamDebateComment $examDebateComment)
    {
        $this->examDebateComment = $examDebateComment;
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

    // 辩论留言创建成功后通知相关用户
    public function toDatabase($notifiable)
    {
        $comment = $this->examDebateComment;
        $exam = Exam::find($comment->exam_id);
        $author = $comment->getAuthor->username;
        $debate = ExamDebate::find($comment->debate_id);
        $link = '/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id.'#comment'.$comment->id;
        // $doer = User::find($this->examDebate->Aauthor_id);
        return [
            'creator_id'    => $comment->author_id,
            'creator'       => $author,
            'link'          => $link,
            'matter'        => '用户['.$author.']回复了你在攻辩：<'.$debate->title.'>发表的评论。'
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
