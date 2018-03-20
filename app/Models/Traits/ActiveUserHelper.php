<?php

namespace App\Models\Traits;

use App\Models\Topic;
use App\Models\Reply;
use Carbon\Carbon;
use Cache;
use DB;

trait ActiveUserHelper
{
    protected $users = [];

    protected $topic_weight = 4; // 话题权重
    protected $reply_weight = 1; // 回复权重
    protected $user_numbers = 6; //显示的用户数量
    protected $pass_days = 7; //多少天内发表过的内容

    // 缓存相关
    protected $cache_key = 'larabbs_active_user';
    protected $cache_expire_in_minutes = 65;


    public function getActiveUsers()
    {
        // 获取缓存用户，如果数据存在则直接返回
        // 否则第三个参数的匿名函数将会被执行
        return Cache::remember($this->cache_key, $this->cache_expire_in_minutes, function() {
            return $this->calculateActiveUsers();
        });
    }


    // 获取活跃用户并缓存起来
    public function calculateAndCacheActiveUsers()
    {
        $active_users = $this->calculateActiveUsers();

        $this->cacheActiveUsers($active_users);
    }

    // 获取缓存数据
    public function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        // 返回的是按照score来进行正序排列的
        $users = array_sort($this->users, function($user) {
            return $user['score'];
        });

        // 我们需要倒序排序的数组
        $users = array_reverse($users, true);

        // 取指定($this->user_numbers)个用户
        $users = array_slice($users, 0, $this->user_numbers, true);

        // 新建一个空集合
        $active_users = collect();

        foreach ($users as $user_id => $user) {
            $user = $this->find($user_id);
            // dump($user);
            if (count($user->toArray())) {
                $active_users->push($user);
            }
        }
        return $active_users; //返回一个集合
        // dd($active_users->toArray());
    }

    private function calculateTopicScore() {
        $topic_users = Topic::query()->select(DB::raw('user_id, count(*) AS topic_count'))
                        ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
                        ->groupBy('user_id')
                        ->get();
        foreach ($topic_users as $topic_user) {
            $this->users[$topic_user['user_id']]['score'] = $this->topic_weight * $topic_user['topic_count'];
        }
        // dump($this->users);
    }

    private function calculateReplyScore() {
        $reply_users = Reply::query()->select(DB::raw('user_id, count(*) AS reply_count'))
                    ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
                    ->groupBy('user_id')
                    ->get();

        foreach ($reply_users as $reply_user) {
            $reply_score = $this->reply_weight * $reply_user['reply_count'];

            if (isset($this->users[$reply_user->user_id])) {
                $this->users[$reply_user->user_id]['score'] += $reply_score;
            } else {
                $this->users[$reply_user->user_id]['score'] = $reply_score;
            }
            
        }
        // dump($this->users);
    }

    // 缓存用户数据
    public function cacheActiveUsers($active_users)
    {
        Cache::put($this->cache_key, $active_users, $this->cache_expire_in_minutes);
    }


}