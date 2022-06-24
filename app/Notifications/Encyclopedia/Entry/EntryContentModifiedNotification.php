<?php

namespace App\Notifications\Encyclopedia\Entry;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\Entry\EntryContent;
use App\Home\Encyclopedia\Entry;
use App\Models\User;

class EntryContentModifiedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryContent $entryContent)
    {
        $this->entryContent = $entryContent;
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

    // 词条正文被编辑后，通知协作组成员和兴趣用户词条已经编辑
    public function toDatabase($notifiable)
    {
        $entry = Entry::find($this->entryContent->eid);
        $user = User::find($this->entryContent->editor_id);
        $link = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        if($this->entryContent->big == '0'){
            $big = '小编辑';
        }else{
            $big = '大编辑';
        }
        return [
            'creator_id'    => $user->id,
            'creator'       => $user->username,
            'link'          => $link,
            'matter'        => '词条《'.$entry->title.'》正文内容已经重新编辑['.$big.']。'
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
