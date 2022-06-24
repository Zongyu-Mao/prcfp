<?php

namespace App\Notifications\Encyclopedia\EntryDebate\EntryDebateComment;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\EntryDebate\EntryDebateComment;

class EntryDebateCommentRepliedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(EntryDebateComment $entryDebateComment)
    {
        $this->entryDebateComment = $entryDebateComment;
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

    // 词条辩论创建成功后通知相关用户
    public function toDatabase($notifiable)
    {
        $comment = $this->entryDebateComment;
        $entry = Entry::find($comment->eid);
        $debate = EntryDebate::find($comment->debate_id);
        $link = '/encyclopedia/debate/'.$entry->id.'/'.$entry->title.'?type='.$debate->type.'&type_id='.$debate->type_id.'#debateComment'.$comment->id;
        // $doer = User::find($this->entryDebate->Aauthor_id);
        return [
            'creator_id'    => $comment->author_id,
            'creator'       => $comment->getAuthor->username,
            'link'          => $link,
            'matter'        => '用户['.$comment->getAuthor->username.']回复了你在攻辩：<'.$debate->title.'>发表的评论。'
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
