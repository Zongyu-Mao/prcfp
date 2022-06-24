<?php

namespace App\Notifications\Personnel\MessageInform;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Personnel\MessageInform;

class MessageInformRejectedToFailureNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(MessageInform $messageInform)
    {
        $this->messageInform = $messageInform;
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
        $inform = $this->messageInform;
        $link = '/personnel/inform/detail/'.$inform->id.'/'.$inform->title.'#messageInform'.$inform->id;
        return [
            'creator_id'    => 0,
            'creator'       => '系统',
            'link'          => $link,
            'matter'        => '你的举报内容<'.$inform->title.'>已经驳回，举报失败。'
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
