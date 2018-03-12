<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'user_id', 'category_id', 'reply_count', 'view_count', 'last_replay_user_id', 'order', 'excerpt', 'slug'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 通过控制器传过来的order进行话题排序
    public function scopeWithOrder($query, $order)
    {
        switch ($order) {
            case 'recent':
                $query = $this->recent();
                break;
            
            default:
                $query = $this->recentReplied();
                break;
        }
        // 这里调用with方法是为了防止N+1的问题
        // dd($query->toSql());
        return $query->with('user', 'category');
    }

    // 按照创建时间倒序排列
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');   
    }

    public function scopeRecentReplied($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }
}
