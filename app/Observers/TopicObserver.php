<?php

namespace App\Observers;

use App\Models\Topic;
use App\Jobs\TranslateSlug;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    //当对topic模型做入库操作(create or update)时触发此事件
    public function saving(Topic $topic)
    {
        $topic->excerpt = make_excerpt($topic->body);

        // 如果topics表中的slug字段没有值那么入库的时候插入
        // $topic->slug = app(\App\Handlers\SlugTranslateHandler::class)->translate($topic->title); // 改用下面的队列方式
    }

    // 当对topic模型数据入库成功以后执行对应事件
    public function saved(Topic $topic)
    {
        if (! $topic->slug) {
            // 使用任务队列的方式
            dispatch(new TranslateSlug($topic));
        }
    }

    // 删除某个话题后，连带删除此话题下的所有评论
    public function deleted(Topic $topic)
    {
        // 重点注意：在模型监听器中不要使用Eloquent
        \DB::table('replies')->where('topic_id', $topic->id)->delete();
    }
}