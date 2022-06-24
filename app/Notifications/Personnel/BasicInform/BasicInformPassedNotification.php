<?php

namespace App\Notifications\Personnel\BasicInform;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Personnel\Inform;

class BasicInformPassedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Inform $inform)
    {
        $this->inform = $inform;
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

    // 举报结果通知
    public function toDatabase($notifiable)
    {
        $inform = $this->inform;
        $link = '/personnel/inform/detail/'.$inform->id.'/'.$inform->title.'#basicInform'.$inform->id;
        return [
            'creator_id'    => 0,
            'creator'       => '系统',
            'link'          => $link,
            'matter'        => '你的举报内容<'.$inform->title.'>已经通过，举报成功。'
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
