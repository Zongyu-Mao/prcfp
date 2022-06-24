<?php

namespace App\Notifications\Examination\ExamDebate\ExamDebateGiveLike;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamDebate;
use App\Home\Examination\Exam;

class ExamDebateGivenLikeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamDebateStarRecord $examDebateStarRecord)
    {
        $this->examDebateStarRecord = $examDebateStarRecord;
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

    // 词条辩论创建成功后通知相关用户
    public function toDatabase($notifiable)
    {
        $record = $this->examDebateStarRecord;
        $debate = ExamDebate::find($record->debate_id);
        $exam = Exam::find($debate->exam_id);
        // 判断立场
        if($record->star == '0'){
            $standpoint = '送了一颗红星星给';
        }elseif($record->star == '1'){
            $standpoint = '送了一颗黑星星给';
        }
        $link = '/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        // $doer = User::find($this->articleDebate->Aauthor_id);
        return [
            'creator_id'    => $record->user_id,
            'creator'       => $record->username,
            'link'          => $link,
            'matter'        => '用户['.$record->username.']'.$standpoint.'你，在你属的攻辩：<'.$debate->title.'>。[来自试卷：《'.$exam->title.'》]。'
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
