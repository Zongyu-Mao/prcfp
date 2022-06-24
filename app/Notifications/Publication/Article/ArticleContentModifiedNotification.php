<?php

namespace App\Notifications\Publication\Article;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\Article\ArticleContent;
use App\Home\Publication\Article;
use App\Home\Classification;
use App\Models\User;

class ArticleContentModifiedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleContent $articleContent)
    {
        $this->articleContent = $articleContent;
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
        $article = Article::find($this->articleContent->aid);
        $link = '/publication/reading/'.$article->id.'/'.$article->title;
        $doer = User::find($this->articleContent->editor_id);
        return [
            'creator_id'    => $doer->id,
            'creator'       => $doer->username,
            'link'          => $link,
            'matter'        => '著作《'. $article->title .'》第'. $this->articleContent->sort .'章节内容：<'. $this->articleContent->title .'>已经重新编辑。'
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
