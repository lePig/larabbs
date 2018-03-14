<?php

namespace App\Models;

class Reply extends Model
{
    protected $fillable = ['content'];


    /**
     * 获取此评论属于(belongTo)哪个用户
     * @date   2018-03-14
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 获取此评论属于哪个话题
     * @date   2018-03-14
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id', 'id');
    }
}
