<?php

namespace App\Notifications\Publication\ArticleReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleReview\ArticleReviewOpponent;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\Article;

class ArticleReviewOpponentCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleReviewOpponent $articleReviewOpponent)
    {
        $this->articleReviewOpponent = $articleReviewOpponent;
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
        $articleReview = ArticleReview::find($this->articleReviewOpponent->rid);
        $article = Article::find($articleReview->aid);
        $link = '/publication/review/'.$article->id.'/'.$article->title.'#opponent'.$this->articleReviewOpponent->id;
        return [
            'creator_id'    => $this->articleReviewOpponent->author_id,
            'creator'       => $this->articleReviewOpponent->author,
            'link'          => $link,
            'matter'        => '你协作著作《'.$article->title.'》的评审计划<'.$articleReview->title.'>新增反对意见<'.$this->articleReviewOpponent->title.'>。'
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
