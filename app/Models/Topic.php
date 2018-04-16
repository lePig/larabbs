<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 这个话题下有多少条评论(一对多)
     * @date   2018-03-14
     */
    public function replies()
    {
        return $this->hasMany(Reply::class, 'topic_id', 'id');
    }

    // 通过控制器传过来的order进行话题排序
    public function scopeWithOrder($query, $order)
    {
        switch ($order) {
            case 'recent':
                $query ->recent();
                break;

            default:
                $query ->recentReplied();
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


    /**
     * 给话题详情页转换友好链接使用
     * @date   2018-03-13
     */
    public function link($params = [])
    {
        // var_dump($params);
        //返回类似如下链接 http://larabbs.test/topics/115/comprehensive-analysis-of-golang-interface
        return route('topics.show', array_merge([$this->id, $this->slug], $params));
    }
}
