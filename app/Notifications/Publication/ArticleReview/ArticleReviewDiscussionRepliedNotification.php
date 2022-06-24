<?php

namespace App\Notifications\Publication\ArticleReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleReview\ArticleReviewDiscussion;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\Article;

class ArticleReviewDiscussionRepliedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleReviewDiscussion $articleReviewDiscussion)
    {
        $this->articleReviewDiscussion = $articleReviewDiscussion;
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

    // 评论成功后对员评论作者的通知
    public function toDatabase($notifiable)
    {
        $discussion = $this->articleReviewDiscussion;
        $articleReview = ArticleReview::find($discussion->rid);
        $article = Article::find($articleReview->aid);
        $link = '/publication/review/'.$article->id.'/'.$article->title.'#discussion'.$discussion->id;
        return [
            'creator_id'    => $discussion->author_id,
            'creator'       => $discussion->getAuthor->username,
            'link'          => $link,
            'matter'        => '你在著作《'.$article->title.'》的评审计划<'.$articleReview->title.'>中发表的评论已经被回复。'
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
