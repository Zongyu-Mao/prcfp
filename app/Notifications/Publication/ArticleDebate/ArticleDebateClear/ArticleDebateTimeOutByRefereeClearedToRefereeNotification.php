<?php

namespace App\Notifications\Publication\ArticleDebate\ArticleDebateClear;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleDebate;
use App\Home\Publication\Article;

class ArticleDebateTimeOutByRefereeClearedToRefereeNotification extends Notification
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

    // 辩论
    public function toDatabase($notifiable)
    {
        $debate = $this->articleDebate;
        $article = Article::find($debate->aid);
        $link = '/publication/debate/'.$article->id.'/'.$article->title.'?type='.$debate->type.'&type_id='.$debate->type_id;
        // $doer = User::find($debate->Aauthor_id);
        $matter = '你裁判的攻辩<'.$debate->title.'>由于你结算超时，已经由系统自动结算。非常遗憾，你将受到系统惩罚，感谢参与！[来自著作：《'.$article->title.'》]。';
        $creator = $debate->referee;
        $creator_id = $debate->referee_id;
        return [
            'creator_id'    => $creator_id,
            'creator'       => $creator,
            'link'          => $link,
            'matter'        => $matter
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
