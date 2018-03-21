<?php

namespace App\Observers;

use App\Models\Link;
use Cache;

class LinkObserver
{
    /**
     * 后台更新link模型后清空缓存
     * @date   2018-03-21
     */
    public function saved(Link $link)
    { 
        // \Log::info(['fuckcccccccccccc', $link->cache_key]);
        Cache::forget($link->cache_key);
    }
}