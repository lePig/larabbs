<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Reply;

class TopicReplied extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reply;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Reply $reply)
    {
        // 注入回复实体，方便 toDatabase 方法中的使用
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // 开启通知的通道
        // return ['database'];
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        //dd($notifiable); //打印的是User模型对象
        $topic = $this->reply->topic;
        $link = $topic->link(['#reply' . $this->reply->id]);

        // 入库 这些字段是我们根据业务自己定的。最后会被转换为json存入notification表的data字段中
        return [
            'reply_id'      => $this->reply->id,
            'reply_content' => $this->reply->content,
            'user_id'       => $this->reply->user->id,
            'user_name'     => $this->reply->user->name,
            'user_avatar'   => $this->reply->user->avatar,
            'topic_link'    => $link,
            'topic_id'      => $topic->id,
            'topic_title'   => $topic->title,
        ];
    }

    /**
     * via函数开启了email 所以需要此函数
     * @date   2018-03-15
     */
    public function toMail($notifiable)
    {
        //url地址形如：http://larabbs.test/topics/101/how-does-golang-get-started?#reply74
        $url = $this->reply->topic->link(['#reply' . $this->reply->id]);
        // \Log::info($url);

        return (new MailMessage)->line('你的话题有了新回复')->action('查看回复', $url);
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
