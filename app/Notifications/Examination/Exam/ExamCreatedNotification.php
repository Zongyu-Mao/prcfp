<?php

namespace App\Notifications\Examination\Exam;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Examination\Exam;
use App\Models\User;

class ExamCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Exam $exam)
    {
        $this->exam = $exam;
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
    // 创建成功后通知作者创建成功
    public function toDatabase($notifiable)
    {
        $exam = $this->exam;
        $link = '/examination/reading/'.$exam->id.'/'.$exam->title;
        $doer = User::find($exam->creator_id);
        return [
            'creator_id'    => $doer->id,
            'creator'       => $doer->username,
            'link'          => $link,
            'matter'        => '你的试卷《'.$exam->title.'》创建成功。'
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
