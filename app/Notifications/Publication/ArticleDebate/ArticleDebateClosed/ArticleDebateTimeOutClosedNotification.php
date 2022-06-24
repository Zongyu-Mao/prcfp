<?php

namespace App\Notifications\Publication\ArticleDebate\ArticleDebateClosed;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleDebate;
use App\Home\Publication\Article;

class ArticleDebateTimeOutClosedNotification extends Notification
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
        if($debate->status == '2'){
            $matter = '攻辩<'.$debate->title.'>由于攻方['.$debate->Aauthor.']回复超时已经关闭。[来自著作：《'.$article->title.'》]。';
            $creator = $debate->Aauthor;
            $creator_id = $debate->Aauthor_id;
        }elseif($debate->status == '3'){
            $matter = '攻辩<'.$debate->title.'>由于辩方['.$debate->Bauthor.']回复超时已经关闭。[来自著作：《'.$article->title.'》]。';
            $creator = $debate->Bauthor;
            $creator_id = $debate->Bauthor_id;
        }
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
