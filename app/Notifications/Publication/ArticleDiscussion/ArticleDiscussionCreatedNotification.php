<?php

namespace App\Notifications\Publication\ArticleDiscussion;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleDiscussion;
use App\Home\Publication\Article;

class ArticleDiscussionCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleDiscussion $articleDiscussion)
    {
        $this->articleDiscussion = $articleDiscussion;
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

    // 词条的反对意见创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $article = Article::find($this->articleDiscussion->aid);
        $link = '/publication/discussion/'.$article->id.'/'.$article->title.'#discussion'.$this->articleDiscussion->id;
        return [
            'creator_id'    => $this->articleDiscussion->author_id,
            'creator'       => $this->articleDiscussion->author,
            'link'          => $link,
            'matter'        => '你协作的著作《'.$article->title.'》新增[普通]讨论内容<'.$this->articleDiscussion->title.'>。'
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
