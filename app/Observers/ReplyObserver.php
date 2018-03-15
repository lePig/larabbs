<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        
    }

    public function updating(Reply $reply)
    {
        //
    }

    public function created(Reply $reply)
    {
        // 回复成功后 话题的回复数+1
        $reply->topic->increment('reply_count', 1);

        // 回复成功后，写入消息通知表并往users表中的notification_count +1
        $reply->topic->user->notify(new TopicReplied($reply));

        //+1的操作放到User模型里写(这里也也是可以的)
        // $reply->topic->user->increment('notification_count', +1);
    }

    public function deleted(Reply $reply)
    {
        // $reply->topic->increment('reply_count', 1);
        $reply->topic->decrement('reply_count', 1); //也可以使用increment('reply_count', -1)
    }
}