<?php

namespace App\Notifications\Publication\ArticleDiscussion;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleDiscussion\ArticleAdvise;
use App\Home\Publication\Article;

class ArticleAdvisementRejectedToUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleAdvise $articleAdvise)
    {
        $this->articleAdvise = $articleAdvise;
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

    // 词条的建议被拒绝后通知原内容作者
    public function toDatabase($notifiable)
    {
        $article = Article::find($this->articleAdvise->aid);
        $pAdvisement = ArticleAdvise::find($this->articleAdvise->pid)->title;
        $link = '/publication/discussion/'.$article->id.'/'.$article->title.'#advise'.$this->articleAdvise->id;
        return [
            'creator_id'    => $this->articleAdvise->author_id,
            'creator'       => $this->articleAdvise->author,
            'link'          => $link,
            'matter'        => '你在著作《'.$article->title.'》讨论中的[建议]立场内容<'.$pAdvisement.'>已被拒绝。理由：<'.$this->articleAdvise->title.'>。'
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
