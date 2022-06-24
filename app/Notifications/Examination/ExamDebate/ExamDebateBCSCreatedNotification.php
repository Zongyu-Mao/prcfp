<?php

namespace App\Notifications\Examination\ExamDebate;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamDebate;
use App\Home\Examination\Exam;

class ExamDebateBCSCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ExamDebate $examDebate)
    {
        $this->examDebate = $examDebate;
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

    // 著作辩论创建成功后通知相关用户
    public function toDatabase($notifiable)
    {
        $debate = $this->examDebate;
        $exam = Exam::find($debate->exam_id);
        $time = Carbon::parse($debate->ACScreateTime)->addDays(5);
        $link = '/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        // $doer = User::find($debate->Aauthor_id);
        return [
            'creator_id'    => $debate->Bauthor_id,
            'creator'       => $debate->Bauthor,
            'link'          => $link,
            'matter'        => '辩方['.$debate->Aauthor.']已经在攻辩：<'.$debate->title.'>发表辩方的总结陈词。你可以在'.$time.'前回复。[来自试卷：《'.$exam->title.'》]。'
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
