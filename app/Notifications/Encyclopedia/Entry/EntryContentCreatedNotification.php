<?php

namespace App\Notifications\Encyclopedia\Entry;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\Entry\EntryContent;
use App\Home\Encyclopedia\Entry;
use App\Home\Classification;
use App\Models\User;

class EntryContentCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(entryContent $entryContent)
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

    // 创建成功后通知对该专业感兴趣的用户新增了内容
    public function toDatabase($notifiable)
    {
        $content = $this->entryContent;
        $entry = Entry::find($content->eid);
        $link = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title;
        $classname = Classification::find($entry->cid)->classname;
        $doer = User::find($content->editor_id);
        return [
            'creator_id'    => $doer->id,
            'creator'       => $doer->username,
            'link'          => $link,
            'matter'        => '词条《'. $entry->title .'》添加了第'. $content->sort .'部分内容。'
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
