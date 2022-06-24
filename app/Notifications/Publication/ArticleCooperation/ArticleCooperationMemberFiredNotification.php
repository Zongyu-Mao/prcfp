<?php

namespace App\Notifications\Publication\ArticleCooperation;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleCooperation\ArticleCooperationUser;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article;

class ArticleCooperationMemberFiredNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleCooperationUser $articleCooperationUser)
    {
        $this->articleCooperationUser = $articleCooperationUser;
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

    // 评审计划创建成功后对相关用户的通知
    public function toDatabase($notifiable)
    {
        $cooperation = ArticleCooperation::find($this->articleCooperationUser->cooperation_id);
        $article = Article::find($cooperation->aid);
        $link = '/publication/cooperation/'.$article->id.'/'.$article->title;
        return [
            'creator_id'    => $cooperation->manage_id,
            'creator'       => $cooperation->manager,
            'link'          => $link,
            'matter'        => '你在词条《'.$article->title.'》协作计划<'.$cooperation->title.'>中的成员资格已经取消。'
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
