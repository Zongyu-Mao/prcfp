<?php

namespace App\Notifications\Publication\ArticleDiscussion;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Publication\Article;

class ArticleOpponentRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleOpponent $articleOpponent)
    {
        $this->articleOpponent = $articleOpponent;
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

    // 反对意见被拒绝后通知原内容作者
    public function toDatabase($notifiable)
    {
        $article = Article::find($this->articleOpponent->aid);
        $pOpponent = ArticleOpponent::find($this->articleOpponent->pid)->title;
        $link = '/publication/discussion/'.$article->id.'/'.$article->title.'#opponent'.$this->articleOpponent->id;
        return [
            'creator_id'    => $this->articleOpponent->author_id,
            'creator'       => $this->articleOpponent->author,
            'link'          => $link,
            'matter'        => '你关注（协作）的著作《'.$article->title.'》的[反对]立场讨论内容<'.$pOpponent.'>已被拒绝，理由：<'.$this->articleOpponent->title.'>。'
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
