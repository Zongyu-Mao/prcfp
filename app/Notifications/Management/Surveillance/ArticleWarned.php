<?php

namespace App\Notifications\Management\Surveillance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Committee\Surveillance\SurveillanceArticleWarning;

class ArticleWarned extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SurveillanceArticleWarning $surveillanceArticleWarning)
    {
        $this->surveillanceArticleWarning = $surveillanceArticleWarning;
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
        $warn = $this->surveillanceArticleWarning;
        $content = $warn->content;
        $link = '/publication/reading/'.$content->id.'/'.$content->title;
        return [
            'creator_id'    => $warn->user_id,
            'creator'       => $warn->author->username,
            'link'          => $link,
            'matter'        => '你协作的著作内容《'.$content->title.'》已被警示。'
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
