<?php

namespace App\Notifications\Publication\ArticleReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleReview\ArticleReviewAdvise;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\Article;

class ArticleReviewAdvisementRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleReviewAdvise $articleReviewAdvise)
    {
        $this->articleReviewAdvise = $articleReviewAdvise;
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

    public function toDatabase($notifiable)
    {
        $articleReview = ArticleReview::find($this->articleReviewAdvise->rid);
        $article = Article::find($articleReview->aid);
        $parentAdvise = ArticleReviewAdvise::find($this->articleReviewAdvise->pid);
        $link = '/publication/review/'.$article->id.'/'.$article->title.'#advise'.$this->articleReviewAdvise->id;
        return [
            'creator_id'    => $this->articleReviewAdvise->author_id,
            'creator'       => $this->articleReviewAdvise->author,
            'link'          => $link,
            'matter'        => '协作著作《'.$article->title.'》的评审计划<'.$articleReview->title.'>中的建议<'.$parentAdvise->title.'>已被拒绝，理由：<'.$this->articleReviewAdvise->title.'>。'
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
