<?php

namespace App\Notifications\Personal\Credit;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Personnel\Level;

class LevelDemotedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(UserLevel $level)
    {
        $this->level = $level;
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
        $level = $this->level;
        $link = '/personnel/myCredit';
        $status = $level->status-1;
        $matter = '你的账户已经降级，当前等级：'.$level->sort.'，当前回扣等级为：'.$status.'。';
        return [
            'creator_id'    => 0,
            'creator'       => '系统',
            'link'          => $link,
            'matter'        => $matter
        ];
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
