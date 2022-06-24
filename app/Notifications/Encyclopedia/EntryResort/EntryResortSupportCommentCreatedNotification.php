<?php

namespace App\Notifications\Encyclopedia\EntryResort;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\EntryResort\EntryResortSupportComment;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\Entry;

class EntryResortSupportCommentCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryResortSupportComment $entryResortSupportComment)
    {
        $this->entryResortSupportComment = $entryResortSupportComment;
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

    // 演示评审计划创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $comment = $this->entryResortSupportComment;
        $resort = EntryResort::find($comment->resortId);
        $entry = Entry::find($resort->eid);
        $link = '/encyclopedia/resort/'.$entry->id.'/'.$entry->title.'#resortComment'.$comment->id;
        if($comment->pid == 0){
            $matter = $comment->getAuthor->username.'回复了你在词条《'.$entry->title.'》发布的帮助内容<'.$resort->title.'>。';
        }else{
            $matter = $comment->getAuthor->username.'回复了你在词条《'.$entry->title.'》帮助内容发布的留言：<'.EntryResortSupportComment::find($comment->pid)->title.'>。';
        }
        
        return [
            'creator_id'    => $comment->author_id,
            'creator'       => $comment->author,
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
