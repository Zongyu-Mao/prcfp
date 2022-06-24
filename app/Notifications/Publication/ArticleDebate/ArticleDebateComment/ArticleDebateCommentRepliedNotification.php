<?php

namespace App\Notifications\Publication\ArticleDebate\ArticleDebateComment;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleDebate\ArticleDebateComment;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate;

class ArticleDebateCommentRepliedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleDebateComment $articleDebateComment)
    {
        $this->articleDebateComment = $articleDebateComment;
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

    // 辩论留言创建成功后通知相关用户
    public function toDatabase($notifiable)
    {
        $comment = $this->articleDebateComment;
        $article = Article::find($comment->aid);
        $author = $comment->getAuthor->username;
        $debate = ArticleDebate::find($comment->debate_id);
        $link = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id.'#comment'.$comment->id;
        // $doer = User::find($this->articleDebate->Aauthor_id);
        return [
            'creator_id'    => $comment->author_id,
            'creator'       => $author,
            'link'          => $link,
            'matter'        => '用户['.$author.']回复了你在攻辩：<'.$debate->title.'>发表的评论。'
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
