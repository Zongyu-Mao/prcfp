<?php

namespace App\Notifications\Examination\ExamDebate\ExamDebateClear;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\ExamDebate;
use App\Home\Examination\Exam;

class ExamDebateTimeOutByRefereeClearedNotification extends Notification
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
    // 辩论结算成功后通知相关用户
    public function toDatabase($notifiable)
    {
        $debate = $this->examDebate;
        $exam = Exam::find($debate->exam_id);
        $link = '/examination/debate/'.$exam->id.'/'.$exam->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        // $doer = User::find($debate->Aauthor_id);
        if($debate->victory == '1'){
            $matter = '攻辩<'.$debate->title.'>已经结算。[裁判'.$debate->referee.']超时，自动结算。攻方['.$debate->Aauthor.']胜出。[来自试卷：《'.$exam->title.'》]。';
            $creator = $debate->Aauthor;
            $creator_id = $debate->Aauthor_id;
        }elseif($debate->victory == '2'){
            $matter = '攻辩<'.$debate->title.'>已经结算。[裁判'.$debate->referee.']超时，自动结算。攻方辩方['.$debate->Bauthor.']胜出。[来自试卷：《'.$exam->title.'》]。';
            $creator = $debate->Bauthor;
            $creator_id = $debate->Bauthor_id;
        }
        return [
            'creator_id'    => $creator_id,
            'creator'       => $creator,
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
