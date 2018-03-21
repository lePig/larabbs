<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Cache;

class Link extends Model
{
    protected $fillable = ['title', 'link'];

    public $cache_key = 'larabbs_links';
    protected $cache_expire_in_minutes = 1440;



    /**
     * 获取侧边栏推荐资源列表
     * @date   2018-03-21
     */
    public function getAllCached()
    {
        // $this->getAllLinks();
        return Cache::remember($this->cache_key, $this->cache_expire_in_minutes, function () {
            return $this->all();
        });
    }
}
