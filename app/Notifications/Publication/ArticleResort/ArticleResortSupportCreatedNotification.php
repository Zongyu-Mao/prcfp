<?php

namespace App\Notifications\Publication\ArticleResort;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleResort;
use App\Home\Publication\Article;

class ArticleResortSupportCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleResort $articleResort)
    {
        $this->articleResort = $articleResort;
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
        $article = Article::find($this->articleResort->aid);
        $parentResort = ArticleResort::find($this->articleResort->pid);
        $link = '/publication/resort/'.$article->id.'/'.$article->title.'#resort'.$this->articleResort->id;
        return [
            'creator_id'    => $this->articleResort->author_id,
            'creator'       => $this->articleResort->author,
            'link'          => $link,
            'matter'        => '你在著作《'.$article->title.'》发布的求助内容<'.$parentResort->title.'>新增了帮助内容：<'.$this->articleResort->title.'>。'
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
