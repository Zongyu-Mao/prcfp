<?php

namespace App\Notifications\Encyclopedia\Entry\EntryExtendedReading;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\Entry\Extended\EntryExtendedExamReading;
use App\Home\Encyclopedia\Entry;
use App\Home\Examination\Exam;
use Illuminate\Support\Facades\Auth;

class EntryExtendedExamReadingBeenDeletedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryExtendedExamReading $entryExtendedExamReading)
    {
        $this->entryExtendedExamReading = $entryExtendedExamReading;
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
        $ex = $this->entryExtendedExamReading;
        $entry = Entry::find($ex->eid);
        $extended = Exam::find($ex->extended_id);
        $link = '/examination/reading/'.$extended->id.'/'.$extended->title.'#entryExtendedExamReading';
        // $doer = User::find($this->entryExtended->creator_id);
        return [
            'creator_id'    => auth('api')->user()->id,
            'creator'       => auth('api')->user()->username,
            'link'          => $link,
            'matter'        => '你关注的试卷《'.$extended->title.'》与词条《'.$entry->title.'》的延伸阅读关系已经解除。'
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
