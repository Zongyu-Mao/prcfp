<?php

namespace App\Notifications\Encyclopedia\Entry\EntryReference;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\Entry\EntryReference;
use App\Home\Encyclopedia\Entry;
use Illuminate\Support\Facades\Auth;

class EntryReferenceDeletedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryReference $entryReference)
    {
        $this->entryReference = $entryReference;
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

    // 参考文献删除后，通知用户参考文献已经删除

     public function toDatabase($notifiable)
    {
        $entry = Entry::find($this->entryReference->entry_id);
        $link = '/encyclopedia/reading/'.$entry->id.'/'.$entry->title.'#reference';
        return [
            'creator_id'    => auth('api')->user()->id,
            'creator'       => auth('api')->user()->username,
            'link'          => $link,
            'matter'        => '你关注的词条《'.$entry->title.'》的参考文献['.$this->entryReference->title.']已经删除。'
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
