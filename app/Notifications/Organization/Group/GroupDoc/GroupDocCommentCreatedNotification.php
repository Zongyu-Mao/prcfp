<?php

namespace App\Notifications\Organization\Group\GroupDoc;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Organization\Group\GroupDoc;
use App\Home\Organization\Group\GroupDoc\GroupDocComment;
use App\Models\User;

class GroupDocCommentCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(GroupDocComment $groupDocComment)
    {
        $this->groupDocComment = $groupDocComment;
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
    // 创建成功后通知创建者创建成功
    public function toDatabase($notifiable)
    {
        $comment = $this->groupDocComment;
        $doc = GroupDoc::find($comment->did);
        $link = '/organization/group/groupDocDetail/'.$doc->id.'/'.$doc->title.'#groupDocComment'.$comment->id;
        $doer = User::find($comment->author_id);
        return [
            'creator_id'    => $doer->id,
            'creator'       => $doer->username,
            'link'          => $link,
            'matter'        => '我的组织文档新发表评论《'.$comment->title.'》。'
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
