<?php

namespace App\Notifications\Management\Surveillance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Committee\Surveillance\SurveillanceExamMark;
use App\Models\User;

class ExamMarked extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SurveillanceExamMark $surveillanceExamMark)
    {
        $this->surveillanceExamMark = $surveillanceExamMark;
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

    public function toDatabase($notifiable)
    {
        $mark = $this->surveillanceExamMark;
        $content = $mark->content;
        $link = '/examination/reading/'.$content->id.'/'.$content->title;
        return [
            'creator_id'    => $mark->user_id,
            'creator'       => $mark->author->username,
            'link'          => $link,
            'matter'        => '你协作的试卷内容《'.$content->title.'》已被标记。'
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
