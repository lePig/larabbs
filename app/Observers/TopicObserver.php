<?php

namespace App\Observers;

use App\Models\Topic;

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
        if (! $topic->slug) {
            $topic->slug = app(\App\Handlers\SlugTranslateHandler::class)->translate($topic->title);
        }
    }
}