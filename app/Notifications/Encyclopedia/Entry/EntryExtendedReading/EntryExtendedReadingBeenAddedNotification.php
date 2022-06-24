<?php

namespace App\Notifications\Encyclopedia\Entry\EntryExtendedReading;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\Entry\Extended\EntryExtendedEntryReading;
use App\Home\Encyclopedia\Entry;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EntryExtendedReadingBeenAddedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryExtendedEntryReading $entryExtendedEntryReading)
    {
        $this->entryExtendedEntryReading = $entryExtendedEntryReading;
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

    // 延伸阅读添加后，通知协作组和关注用户已添加
    public function toDatabase($notifiable)
    {
        $ex = $this->entryExtendedEntryReading;
        $entry = Entry::find($ex->eid);
        $extended = Entry::find($ex->extended_id);
        $link = '/encyclopedia/reading/'.$extended->id.'/'.$extended->title.'#entryExtendedEntryReading';
        $doer = User::find($ex->creator_id);
        return [
            'creator_id'    => $doer->id,
            'creator'       => $doer->username,
            'link'          => $link,
            'matter'        => '你关注的词条《'.$extended->title.'》已经被词条《'.$entry->title.'》添加为延伸阅读。'
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
