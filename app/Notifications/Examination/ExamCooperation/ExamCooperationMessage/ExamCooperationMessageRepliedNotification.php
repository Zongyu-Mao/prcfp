<?php

namespace App\Notifications\Examination\ExamCooperation\ExamCooperationMessage;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamCooperation\ExamCooperationMessage;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Exam;

class ExamCooperationMessageRepliedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamCooperationMessage $examCooperationMessage)
    {
        $this->examCooperationMessage = $examCooperationMessage;
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

    // 创建成功后通知对该专业感兴趣的用户新增了内容
    public function toDatabase($notifiable)
    {
        $message = $this->examCooperationMessage;
        $cooperation = ExamCooperation::find($message->cooperation_id);
        $parentMessage = ExamCooperationMessage::find($message->pid);
        $exam = Exam::find($cooperation->exam_id);
        $link = '/examination/cooperation/'.$exam->id.'/'.$exam->title.'#message'.$message->id;
        return [
            'creator_id'    => $message->author_id,
            'creator'       => $message->author,
            'link'          => $link,
            'matter'        => '你在试卷《'.$exam->title.'》的协作计划<'.$cooperation->title.'>中发表的留言信息<'.$parentMessage->title.'>已被['.$message->author.']回复：<'.$message->title.'>。'
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
