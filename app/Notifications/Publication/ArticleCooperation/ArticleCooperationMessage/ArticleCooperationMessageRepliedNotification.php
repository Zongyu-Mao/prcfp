<?php

namespace App\Notifications\Publication\ArticleCooperation\ArticleCooperationMessage;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Home\Publication\ArticleCooperation\ArticleCooperationMessage;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article;

class ArticleCooperationMessageRepliedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ArticleCooperationMessage $articleCooperationMessage)
    {
        $this->articleCooperationMessage = $articleCooperationMessage;
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
        $cooperation = ArticleCooperation::find($this->articleCooperationMessage->cooperation_id);
        $parentMessage = ArticleCooperationMessage::find($this->articleCooperationMessage->pid);
        $article = Article::find($cooperation->aid);
        $link = '/publication/cooperation/'.$article->id.'/'.$article->title.'#message'.$this->articleCooperationMessage->id;
        return [
            'creator_id'    => $this->articleCooperationMessage->author_id,
            'creator'       => $this->articleCooperationMessage->author,
            'link'          => $link,
            'matter'        => '你在著作《'.$article->title.'》的协作计划<'.$cooperation->title.'>中发表的留言信息<'.$parentMessage->title.'>已被['.$this->articleCooperationMessage->author.']回复：<'.$this->articleCooperationMessage->title.'>。'
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
