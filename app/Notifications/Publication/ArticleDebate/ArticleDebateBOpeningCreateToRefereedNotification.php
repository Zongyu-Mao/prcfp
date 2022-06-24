<?php

namespace App\Notifications\Publication\ArticleDebate;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleDebate;
use App\Home\Publication\Article;

class ArticleDebateBOpeningCreateToRefereedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleDebate $articleDebate)
    {
        $this->articleDebate = $articleDebate;
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

    // 词条辩论创建成功后通知相关用户
    public function toDatabase($notifiable)
    {
        $debate = $this->articleDebate;
        $article = Article::find($debate->aid);
        $link = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        // $doer = User::find($debate->Aauthor_id);
        return [
            'creator_id'    => $debate->Bauthor_id,
            'creator'       => $debate->Bauthor,
            'link'          => $link,
            'matter'        => '['.$debate->Bauthor.']已经在你裁判的攻辩：<'.$debate->title.'>发表辩方的开篇陈词。[来自著作：《'.$article->title.'》]。'
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
