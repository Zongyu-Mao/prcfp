<?php

namespace App\Notifications\Management\Committee;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Management\Role\RoleJudgeRecord;
use App\Models\User;

class RoleJudgedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(RoleJudgeRecord $roleJudgeRecord)
    {
        $this->roleJudgeRecord = $roleJudgeRecord;
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
        $record = $this->roleJudgeRecord;
        $user = User::find($record->user_id);
        $link = '/personal';
        $matter = $record->handle_id!=0?'你的角色已经被清空':'你已成功退出角色';
        return [
            'creator_id'    => $user->id,
            'creator'       => $user->username,
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
