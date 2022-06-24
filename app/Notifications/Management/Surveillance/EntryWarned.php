<?php

namespace App\Notifications\Management\Surveillance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Committee\Surveillance\SurveillanceWarning;

class EntryWarned extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(SurveillanceWarning $surveillanceWarning)
    {
        $this->surveillanceWarning = $surveillanceWarning;
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
        $warn = $this->surveillanceWarning;
        $content = $warn->content;
        $link = '/encyclopedia/reading/'.$content->id.'/'.$content->title;
        $matter = $warn->status==0?'你协作的百科内容《'.$content->title.'》已被警示。':$warn->status==2?'你协作的百科内容《'.$content->title.'》警示已撤销。':'你巡查警示的百科内容《'.$content->title.'》申请警示撤销。';
        return [
            'creator_id'    => $warn->user_id,
            'creator'       => $warn->author->username,
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
